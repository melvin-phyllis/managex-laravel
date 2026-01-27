<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\GlobalDocument;
use Illuminate\Support\Facades\Storage;

class GlobalDocumentController extends Controller
{
    /**
     * Liste des documents globaux pour l'employé
     */
    public function index()
    {
        $documents = GlobalDocument::active()
            ->orderBy('type')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($doc) {
                $doc->is_acknowledged = $doc->isAcknowledgedBy(auth()->user());
                return $doc;
            });

        $types = GlobalDocument::getTypes();

        return view('employee.global-documents.index', compact('documents', 'types'));
    }

    /**
     * Voir un document
     */
    public function show(GlobalDocument $globalDocument)
    {
        if (!$globalDocument->is_active) {
            abort(404);
        }

        $isAcknowledged = $globalDocument->isAcknowledgedBy(auth()->user());

        return view('employee.global-documents.show', compact('globalDocument', 'isAcknowledged'));
    }

    /**
     * Télécharger un document
     */
    public function download(GlobalDocument $globalDocument)
    {
        if (!$globalDocument->is_active) {
            abort(404);
        }

        if (!$globalDocument->fileExists()) {
            abort(404, 'Fichier introuvable');
        }

        return Storage::disk('documents')->download(
            $globalDocument->file_path,
            $globalDocument->original_filename
        );
    }

    /**
     * Accuser réception d'un document
     */
    public function acknowledge(GlobalDocument $globalDocument)
    {
        if (!$globalDocument->is_active) {
            abort(404);
        }

        $globalDocument->acknowledge(auth()->user());

        return back()->with('success', 'Accusé de réception enregistré. Merci !');
    }
}
