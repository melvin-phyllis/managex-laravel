<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ContractAcceptanceController extends Controller
{
    /**
     * Sert le PDF du contrat inline pour affichage dans l'iframe.
     */
    public function viewPdf()
    {
        $contract = auth()->user()->currentContract;

        if (! $contract || ! $contract->document_path) {
            abort(404);
        }

        $disk = Storage::disk('documents');

        if (! $disk->exists($contract->document_path)) {
            abort(404);
        }

        $file = $disk->get($contract->document_path);
        $mimeType = $disk->mimeType($contract->document_path);
        $filename = $contract->document_original_name ?? 'contrat.pdf';

        return response($file, 200)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="'.$filename.'"');
    }

    /**
     * Enregistre l'acceptation du contrat.
     */
    public function accept()
    {
        $contract = auth()->user()->currentContract;

        if (! $contract || ! $contract->needsAcceptance()) {
            return redirect()->route('employee.dashboard');
        }

        $contract->update([
            'contract_accepted_at' => now(),
        ]);

        return redirect()->route('employee.dashboard')
            ->with('success', 'Contrat de travail accepté. Bienvenue !');
    }

    /**
     * Refuse le contrat et déconnecte l'employé.
     */
    public function refuse(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('info', 'Vous avez refusé le contrat. Veuillez contacter les RH.');
    }
}
