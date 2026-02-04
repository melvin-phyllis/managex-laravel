<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use Illuminate\Http\Request;

class DocumentRequestController extends Controller
{
    /**
     * Liste des demandes de l'employé
     */
    public function index()
    {
        $requests = DocumentRequest::forUser(auth()->id())
            ->with('admin')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('employee.document-requests.index', compact('requests'));
    }

    /**
     * Formulaire de nouvelle demande
     */
    public function create()
    {
        $types = DocumentRequest::getTypes();

        return view('employee.document-requests.create', compact('types'));
    }

    /**
     * Enregistrer une nouvelle demande
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:'.implode(',', array_keys(DocumentRequest::getTypes())),
            'message' => 'nullable|string|max:1000',
        ], [
            'type.required' => 'Veuillez sélectionner un type de document.',
            'type.in' => 'Type de document invalide.',
        ]);

        DocumentRequest::create([
            'user_id' => auth()->id(),
            'type' => $request->type,
            'message' => $request->message,
            'status' => DocumentRequest::STATUS_PENDING,
        ]);

        return redirect()->route('employee.document-requests.index')
            ->with('success', 'Votre demande a été envoyée. Vous serez notifié dès qu\'elle sera traitée.');
    }

    /**
     * Télécharger le document joint
     */
    public function download(DocumentRequest $documentRequest)
    {
        // Vérifier que c'est bien la demande de l'employé
        if ($documentRequest->user_id !== auth()->id()) {
            abort(403);
        }

        if (! $documentRequest->hasDocument()) {
            abort(404, 'Aucun document attaché');
        }

        return \Storage::disk('documents')->download(
            $documentRequest->document_path,
            $documentRequest->document_name
        );
    }
}
