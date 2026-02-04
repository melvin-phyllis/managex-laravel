<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentType;
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
     * Mes documents contractuels
     */
    public function index()
    {
        $user = auth()->user();

        // Types de documents que l'employé peut uploader (CV, etc.)
        // Exclure les types gérés ailleurs (contrat, règlement, fiche de poste)
        $excludedSlugs = ['work_contract', 'internal_rules', 'job_description', 'contract_amendment', 'work_certificate'];

        $documentTypes = DocumentType::active()
            ->employeeUploadable()
            ->whereNotIn('slug', $excludedSlugs)
            ->ordered()
            ->get();

        // Documents de l'utilisateur par type (dernier document pour chaque type)
        $userDocuments = Document::forUser($user)
            ->with(['type'])
            ->get()
            ->keyBy('document_type_id');

        // Contract document from user's current contract
        $contract = $user->currentContract;
        $hasContractDocument = $contract && $contract->document_path;

        // Global documents requiring acknowledgment
        $globalDocuments = \App\Models\GlobalDocument::active()
            ->with('position')
            ->orderBy('type')
            ->get();

        // Check which global docs the user has acknowledged
        $acknowledgedIds = $user->id ? \DB::table('global_document_acknowledgments')
            ->where('user_id', $user->id)
            ->pluck('global_document_id')
            ->toArray() : [];

        return view('employee.documents.index', compact(
            'documentTypes',
            'userDocuments',
            'contract',
            'hasContractDocument',
            'globalDocuments',
            'acknowledgedIds'
        ));
    }

    /**
     * Détails d'un document
     */
    public function show(Document $document)
    {
        $this->authorize('view', $document);

        $document->load(['type.category', 'uploader', 'validator']);

        return view('employee.documents.show', compact('document'));
    }

    /**
     * Formulaire d'upload
     */
    public function create(DocumentType $type)
    {
        // Vérifier que l'employé peut uploader ce type
        if (! $type->employee_can_upload) {
            abort(403, 'Vous ne pouvez pas uploader ce type de document.');
        }

        $type->load('category');

        return view('employee.documents.create', compact('type'));
    }

    /**
     * Enregistrer un document uploadé
     */
    public function store(Request $request)
    {
        $request->validate([
            'document_type_id' => 'required|exists:document_types,id',
            'file' => 'required|file|max:10240',
            'description' => 'nullable|string|max:500',
            'expiry_date' => 'nullable|date|after:today',
        ]);

        $type = DocumentType::findOrFail($request->document_type_id);

        // Vérifier que l'employé peut uploader
        if (! $type->employee_can_upload) {
            abort(403, 'Vous ne pouvez pas uploader ce type de document.');
        }

        $user = auth()->user();

        try {
            $document = $this->documentService->upload(
                $request->file('file'),
                $user,
                $type,
                $user,
                [
                    'description' => $request->description,
                    'expiry_date' => $request->expiry_date,
                ]
            );

            $message = $type->requires_validation
                ? 'Document envoyé. En attente de validation par les RH.'
                : 'Document ajouté avec succès.';

            return redirect()->route('employee.documents.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return back()->withErrors(['file' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Télécharger un document
     */
    public function download(Document $document)
    {
        $this->authorize('view', $document);

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
     * Supprimer un document (si autorisé)
     */
    public function destroy(Document $document)
    {
        $this->authorize('delete', $document);

        // Vérifier que le type autorise la suppression par l'employé
        if (! $document->type->employee_can_delete) {
            abort(403, 'Vous ne pouvez pas supprimer ce document.');
        }

        // Ne pas supprimer si déjà approuvé
        if ($document->status === 'approved') {
            return back()->withErrors(['error' => 'Impossible de supprimer un document approuvé.']);
        }

        $document->delete();

        return back()->with('success', 'Document supprimé.');
    }

    /**
     * Accuser réception d'un document (règlement intérieur)
     */
    public function acknowledge(Document $document)
    {
        $this->authorize('view', $document);

        if (! $document->requires_acknowledgment) {
            return back()->withErrors(['error' => 'Ce document ne nécessite pas d\'accusé.']);
        }

        if ($document->acknowledged_at) {
            return back()->withErrors(['error' => 'Vous avez déjà accusé réception de ce document.']);
        }

        $document->acknowledge();

        return back()->with('success', 'Accusé de réception enregistré. Merci !');
    }

    /**
     * Download the employee's contract document
     */
    public function downloadContract()
    {
        $user = auth()->user();
        $contract = $user->currentContract;

        if (! $contract || ! $contract->document_path) {
            abort(404, 'Document de contrat introuvable');
        }

        if (! Storage::disk('documents')->exists($contract->document_path)) {
            abort(404, 'Fichier introuvable');
        }

        return Storage::disk('documents')->download(
            $contract->document_path,
            $contract->document_original_name
        );
    }
}
