<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\DocumentType;
use App\Models\User;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    protected DocumentService $documentService;

    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    /**
     * Liste tous les documents avec vue par employé
     */
    public function index(Request $request)
    {
        $query = Document::with(['user', 'type', 'uploader']);

        // Filtres
        if ($request->filled('type')) {
            $query->whereHas('type', function ($q) use ($request) {
                $q->where('slug', $request->type);
            });
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%')
                    ->orWhere('original_filename', 'like', '%'.$request->search.'%')
                    ->orWhereHas('user', function ($uq) use ($request) {
                        $uq->where('name', 'like', '%'.$request->search.'%');
                    });
            });
        }

        // PERFORMANCE: utiliser paginate() au lieu de get() pour éviter de charger tous les documents en mémoire
        $documents = $query->orderBy('created_at', 'desc')->paginate(20);

        // Stats simples
        $stats = [
            'total' => Document::count(),
            'contracts' => Document::whereHas('type', fn ($q) => $q->where('slug', 'work_contract'))->count(),
        ];

        $documentTypes = DocumentType::active()->ordered()->get();
        $employees = User::where('role', 'employee')->with(['position', 'currentContract'])->orderBy('name')->get();

        // Get active global règlement for status check
        $activeReglement = \App\Models\GlobalDocument::active()
            ->ofType(\App\Models\GlobalDocument::TYPE_REGLEMENT_INTERIEUR)
            ->latest()
            ->first();

        return view('admin.documents.index', compact('documents', 'stats', 'documentTypes', 'employees', 'activeReglement'));
    }

    /**
     * Documents en attente de validation
     */
    public function pending()
    {
        $documents = Document::pending()
            ->with(['user', 'type.category', 'uploader'])
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        return view('admin.documents.pending', compact('documents'));
    }

    /**
     * Détails d'un document
     */
    public function show(Document $document)
    {
        $document->load(['user', 'type.category', 'uploader', 'validator']);

        return view('admin.documents.show', compact('document'));
    }

    /**
     * Formulaire upload pour un employé
     */
    public function createForEmployee(User $user)
    {
        // Types que l'admin peut uploader (employee_can_upload = false OU tous)
        $categories = DocumentCategory::active()
            ->with(['activeTypes'])
            ->ordered()
            ->get();

        return view('admin.documents.upload', compact('user', 'categories'));
    }

    /**
     * Upload document pour employé
     */
    public function storeForEmployee(Request $request, User $user)
    {
        $request->validate([
            'document_type_id' => 'required|exists:document_types,id',
            'file' => 'required|file|max:10240', // 10MB max
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'document_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:today',
        ]);

        $type = DocumentType::findOrFail($request->document_type_id);

        try {
            $document = $this->documentService->upload(
                $request->file('file'),
                $user,
                $type,
                auth()->user(),
                [
                    'title' => $request->title,
                    'description' => $request->description,
                    'document_date' => $request->document_date,
                    'expiry_date' => $request->expiry_date,
                ]
            );

            return redirect()->route('admin.employees.show', $user)
                ->with('success', 'Document "'.$document->title.'" ajouté avec succès.');
        } catch (\Exception $e) {
            return back()->withErrors(['file' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Valider un document (approuver/rejeter)
     */
    public function validate(Request $request, Document $document)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'rejection_reason' => 'required_if:action,reject|nullable|string|max:500',
        ]);

        if ($request->action === 'approve') {
            $document->approve(auth()->user());
            $message = 'Document approuvé avec succès.';
        } else {
            $document->reject(auth()->user(), $request->rejection_reason);
            $message = 'Document rejeté.';
        }

        // TODO: Notification à l'employé

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'status' => $document->status,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Validation en masse
     */
    public function bulkValidate(Request $request)
    {
        $request->validate([
            'document_ids' => 'required|array',
            'document_ids.*' => 'exists:documents,id',
            'action' => 'required|in:approve,reject',
            'rejection_reason' => 'required_if:action,reject|nullable|string|max:500',
        ]);

        $documents = Document::whereIn('id', $request->document_ids)
            ->where('status', 'pending')
            ->get();

        $count = 0;
        foreach ($documents as $document) {
            if ($request->action === 'approve') {
                $document->approve(auth()->user());
            } else {
                $document->reject(auth()->user(), $request->rejection_reason);
            }
            $count++;
        }

        $action = $request->action === 'approve' ? 'approuvés' : 'rejetés';

        return back()->with('success', "{$count} documents {$action}.");
    }

    /**
     * Télécharger un document
     */
    public function download(Document $document)
    {
        if (! $document->fileExists()) {
            abort(404, 'Fichier introuvable');
        }

        $document->incrementDownloads();

        return Storage::disk('documents')->download(
            $document->file_path,
            $document->original_filename
        );
    }

    /**
     * Supprimer un document
     */
    public function destroy(Document $document)
    {
        $title = $document->title;
        $document->delete();

        return back()->with('success', 'Document "'.$title.'" supprimé.');
    }

    /**
     * Documents expirant bientôt
     */
    public function expiring()
    {
        $documents = Document::approved()
            ->expiringSoon(60)
            ->with(['user', 'type'])
            ->orderBy('expiry_date', 'asc')
            ->paginate(20);

        return view('admin.documents.expiring', compact('documents'));
    }
}
