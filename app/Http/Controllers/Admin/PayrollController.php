<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\User;
use App\Notifications\PayrollAddedNotification;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $query = Payroll::with('user');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('mois')) {
            $query->where('mois', $request->mois);
        }

        if ($request->filled('annee')) {
            $query->where('annee', $request->annee);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $payrolls = $query->orderBy('annee', 'desc')
                         ->orderBy('mois', 'desc')
                         ->paginate(15);

        $employees = User::where('role', 'employee')->orderBy('name')->get();

        return view('admin.payrolls.index', compact('payrolls', 'employees'));
    }

    public function create()
    {
        $employees = User::where('role', 'employee')->orderBy('name')->get();
        return view('admin.payrolls.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'mois' => ['required', 'integer', 'min:1', 'max:12'],
            'annee' => ['required', 'integer', 'min:2020', 'max:2100'],
            'montant' => ['required', 'numeric', 'min:0'],
            'statut' => ['required', 'in:paid,pending'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Vérifier si une fiche de paie existe déjà pour cet employé/mois/année
        $exists = Payroll::where('user_id', $request->user_id)
            ->where('mois', $request->mois)
            ->where('annee', $request->annee)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'mois' => 'Une fiche de paie existe déjà pour cet employé pour ce mois et cette année.'
            ]);
        }

        $payroll = Payroll::create($request->only([
            'user_id', 'mois', 'annee', 'montant', 'statut', 'notes'
        ]));

        // Générer le PDF automatiquement
        $this->generatePdf($payroll);

        // Envoyer notification
        $payroll->user->notify(new PayrollAddedNotification($payroll));

        return redirect()->route('admin.payrolls.index')
            ->with('success', 'Fiche de paie créée avec succès.');
    }

    public function show(Payroll $payroll)
    {
        $payroll->load('user');
        return view('admin.payrolls.show', compact('payroll'));
    }

    public function edit(Payroll $payroll)
    {
        $employees = User::where('role', 'employee')->orderBy('name')->get();
        return view('admin.payrolls.edit', compact('payroll', 'employees'));
    }

    public function update(Request $request, Payroll $payroll)
    {
        $request->validate([
            'montant' => ['required', 'numeric', 'min:0'],
            'statut' => ['required', 'in:paid,pending'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $payroll->update($request->only(['montant', 'statut', 'notes']));

        // Regénérer le PDF
        $this->generatePdf($payroll);

        return redirect()->route('admin.payrolls.index')
            ->with('success', 'Fiche de paie mise à jour avec succès.');
    }

    public function destroy(Payroll $payroll)
    {
        // Supprimer le PDF associé
        if ($payroll->pdf_url) {
            Storage::disk('public')->delete($payroll->pdf_url);
        }

        $payroll->delete();

        return redirect()->route('admin.payrolls.index')
            ->with('success', 'Fiche de paie supprimée avec succès.');
    }

    public function downloadPdf(Payroll $payroll)
    {
        if (!$payroll->pdf_url || !Storage::disk('public')->exists($payroll->pdf_url)) {
            $this->generatePdf($payroll);
        }

        return Storage::disk('public')->download($payroll->pdf_url);
    }

    public function markAsPaid(Payroll $payroll)
    {
        $payroll->update(['statut' => 'paid']);

        return redirect()->back()->with('success', 'Fiche de paie marquée comme payée.');
    }

    private function generatePdf(Payroll $payroll): void
    {
        $payroll->load('user');

        $pdf = Pdf::loadView('pdf.payroll', [
            'payroll' => $payroll,
            'user' => $payroll->user,
            'generatedAt' => now(),
        ]);

        $filename = "payrolls/payroll_{$payroll->user_id}_{$payroll->mois}_{$payroll->annee}.pdf";

        Storage::disk('public')->put($filename, $pdf->output());

        $payroll->update(['pdf_url' => $filename]);
    }
}
