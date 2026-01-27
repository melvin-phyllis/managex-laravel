<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GlobalDocument;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GlobalDocumentController extends Controller
{
    /**
     * Liste des documents globaux
     */
    public function index()
    {
        $documents = GlobalDocument::with('uploader')
            ->orderBy('type')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('type');

        $types = GlobalDocument::getTypes();

        return view('admin.global-documents.index', compact('documents', 'types'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        $types = GlobalDocument::getTypes();
        $positions = Position::with('department')->orderBy('name')->get();
        return view('admin.global-documents.create', compact('types', 'positions'));
    }

    /**
     * Enregistrer un nouveau document global
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:' . implode(',', array_keys(GlobalDocument::getTypes())),
            'position_id' => 'nullable|exists:positions,id',
            'description' => 'nullable|string|max:1000',
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'is_active' => 'boolean',
        ]);

        $file = $request->file('file');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = 'global/' . $request->type . '/' . $filename;

        Storage::disk('documents')->putFileAs(
            'global/' . $request->type,
            $file,
            $filename
        );

        GlobalDocument::create([
            'title' => $request->title,
            'type' => $request->type,
            'position_id' => $request->type === GlobalDocument::TYPE_FICHE_POSTE ? $request->position_id : null,
            'description' => $request->description,
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'is_active' => $request->boolean('is_active', true),
            'uploaded_by' => auth()->id(),
        ]);

        return redirect()->route('admin.global-documents.index')
            ->with('success', 'Document "' . $request->title . '" ajouté avec succès.');
    }

    /**
     * Détails d'un document
     */
    public function show(GlobalDocument $globalDocument)
    {
        $globalDocument->load(['uploader', 'acknowledgedBy']);
        $usersNotAcknowledged = $globalDocument->usersNotAcknowledged();

        return view('admin.global-documents.show', compact('globalDocument', 'usersNotAcknowledged'));
    }

    /**
     * Formulaire d'édition
     */
    public function edit(GlobalDocument $globalDocument)
    {
        $types = GlobalDocument::getTypes();
        $positions = Position::with('department')->orderBy('name')->get();
        return view('admin.global-documents.edit', compact('globalDocument', 'types', 'positions'));
    }

    /**
     * Mettre à jour un document
     */
    public function update(Request $request, GlobalDocument $globalDocument)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'is_active' => 'boolean',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
        ];

        // Si nouveau fichier, remplacer l'ancien
        if ($request->hasFile('file')) {
            // Supprimer ancien fichier
            $globalDocument->deleteFile();

            $file = $request->file('file');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = 'global/' . $globalDocument->type . '/' . $filename;

            Storage::disk('documents')->putFileAs(
                'global/' . $globalDocument->type,
                $file,
                $filename
            );

            $data['file_path'] = $path;
            $data['original_filename'] = $file->getClientOriginalName();
            $data['mime_type'] = $file->getMimeType();
            $data['file_size'] = $file->getSize();
        }

        $globalDocument->update($data);

        return redirect()->route('admin.global-documents.index')
            ->with('success', 'Document mis à jour.');
    }

    /**
     * Télécharger un document
     */
    public function download(GlobalDocument $globalDocument)
    {
        if (!$globalDocument->fileExists()) {
            abort(404, 'Fichier introuvable');
        }

        return Storage::disk('documents')->download(
            $globalDocument->file_path,
            $globalDocument->original_filename
        );
    }

    /**
     * Supprimer un document
     */
    public function destroy(GlobalDocument $globalDocument)
    {
        $title = $globalDocument->title;
        $globalDocument->delete();

        return redirect()->route('admin.global-documents.index')
            ->with('success', 'Document "' . $title . '" supprimé.');
    }
}
