<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentRequestController extends Controller
{
    /**
     * Liste des demandes de documents
     */
    public function index(Request $request)
    {
        $query = DocumentRequest::with(['user', 'admin']);

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Par défaut, afficher les demandes en attente
            $query->pending();
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(20);
        $statuses = DocumentRequest::getStatuses();

        // Stats
        $stats = [
            'pending' => DocumentRequest::pending()->count(),
            'approved' => DocumentRequest::approved()->count(),
            'total' => DocumentRequest::count(),
        ];

        return view('admin.document-requests.index', compact('requests', 'statuses', 'stats'));
    }

    /**
     * Voir une demande
     */
    public function show(DocumentRequest $documentRequest)
    {
        $documentRequest->load(['user.position', 'admin']);
        return view('admin.document-requests.show', compact('documentRequest'));
    }

    /**
     * Répondre à une demande (approuver)
     */
    public function respond(Request $request, DocumentRequest $documentRequest)
    {
        $request->validate([
            'admin_response' => 'required|string|max:1000',
            'document' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ], [
            'admin_response.required' => 'Veuillez saisir un message de réponse.',
            'document.required' => 'Veuillez joindre le document demandé.',
            'document.mimes' => 'Le document doit être au format PDF, DOC ou DOCX.',
            'document.max' => 'Le document ne doit pas dépasser 10 Mo.',
        ]);

        $file = $request->file('document');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = 'document-requests/' . $documentRequest->user_id . '/' . $filename;

        Storage::disk('documents')->putFileAs(
            'document-requests/' . $documentRequest->user_id,
            $file,
            $filename
        );

        $documentRequest->update([
            'status' => DocumentRequest::STATUS_APPROVED,
            'admin_id' => auth()->id(),
            'admin_response' => $request->admin_response,
            'document_path' => $path,
            'document_name' => $file->getClientOriginalName(),
            'responded_at' => now(),
        ]);

        return redirect()->route('admin.document-requests.index')
            ->with('success', 'Demande approuvée et document envoyé à l\'employé.');
    }

    /**
     * Rejeter une demande
     */
    public function reject(Request $request, DocumentRequest $documentRequest)
    {
        $request->validate([
            'admin_response' => 'required|string|max:1000',
        ], [
            'admin_response.required' => 'Veuillez indiquer le motif du refus.',
        ]);

        $documentRequest->update([
            'status' => DocumentRequest::STATUS_REJECTED,
            'admin_id' => auth()->id(),
            'admin_response' => $request->admin_response,
            'responded_at' => now(),
        ]);

        return redirect()->route('admin.document-requests.index')
            ->with('success', 'Demande refusée.');
    }
}
