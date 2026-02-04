<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\User;
use App\Notifications\PayrollAddedNotification;
use App\Services\Payroll\PayrollService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PayrollController extends Controller
{
    protected $payrollService;

    public function __construct(PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

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
        $employees = User::where('role', 'employee')->with('currentContract')->orderBy('name')->get();

        return view('admin.payrolls.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'mois' => ['required', 'integer', 'min:1', 'max:12'],
            'annee' => ['required', 'integer', 'min:2020', 'max:2100'],
            // Champs additionnels
            'transport_allowance' => ['nullable', 'numeric', 'min:0'],
            'housing_allowance' => ['nullable', 'numeric', 'min:0'],
            'overtime_amount' => ['nullable', 'numeric', 'min:0'],
            'bonuses' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $user = User::findOrFail($validatedData['user_id']);
            $payroll = $this->payrollService->calculatePayroll(
                $user,
                $validatedData['mois'],
                $validatedData['annee'],
                $validatedData
            );

            // Mettre à jour les notes
            $payroll->update(['notes' => $validatedData['notes'] ?? null]);

            // Générer le PDF
            $this->generatePdf($payroll);

            // Notification
            $payroll->user->notify(new PayrollAddedNotification($payroll));

            return redirect()->route('admin.payrolls.index')
                ->with('success', 'Bulletin de paie généré avec succès.');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function calculatePreview(Request $request)
    {
        // TODO: Implémenter une prévisualisation AJAX si demandé
        $request->validate([
            'user_id' => 'required',
            'mois' => 'required',
            'annee' => 'required',
        ]);

        // Similaire store mais sans save (si le service supporte dry-run)
        // Pour l'instant on skip
    }

    public function bulkGenerate(Request $request)
    {
        $request->validate([
            'mois' => 'required|integer',
            'annee' => 'required|integer',
        ]);

        $users = User::where('role', 'employee')
            ->whereHas('currentContract', fn ($q) => $q->active())
            ->get();

        $count = 0;
        foreach ($users as $user) {
            try {
                $payroll = $this->payrollService->calculatePayroll(
                    $user,
                    $request->mois,
                    $request->annee,
                    [] // Pas de primes variables par défaut en bulk
                );
                $this->generatePdf($payroll);
                $count++;
            } catch (\Exception $e) {
                // Log error or continue
            }
        }

        return back()->with('success', "$count bulletins de paie générés.");
    }

    public function show(Payroll $payroll)
    {
        $payroll->load(['user', 'items']);

        return view('admin.payrolls.show', compact('payroll'));
    }

    public function destroy(Payroll $payroll)
    {
        if ($payroll->pdf_url) {
            Storage::disk('public')->delete($payroll->pdf_url);
        }
        $payroll->delete();

        return redirect()->route('admin.payrolls.index')->with('success', 'Bulletin supprimé.');
    }

    /**
     * Formulaire d'édition d'une fiche de paie
     */
    public function edit(Payroll $payroll)
    {
        $payroll->load(['user.currentContract', 'items']);

        return view('admin.payrolls.edit', compact('payroll'));
    }

    /**
     * Mise à jour d'une fiche de paie
     */
    public function update(Request $request, Payroll $payroll)
    {
        $validated = $request->validate([
            'taxable_gross' => 'nullable|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'bonuses' => 'nullable|numeric|min:0',
            'tax_is' => 'nullable|numeric|min:0',
            'tax_cn' => 'nullable|numeric|min:0',
            'tax_igr' => 'nullable|numeric|min:0',
            'cnps_employee' => 'nullable|numeric|min:0',
            'total_deductions' => 'nullable|numeric|min:0',
            'net_salary' => 'nullable|numeric|min:0',
            'workflow_status' => 'nullable|in:draft,pending_review,validated',
            'notes' => 'nullable|string|max:1000',
        ]);

        $payroll->update($validated);

        // Si action = validate, on génère le PDF et on marque comme validé
        if ($request->action === 'validate') {
            $payroll->update([
                'workflow_status' => 'validated',
                'validated_at' => now(),
                'validated_by' => auth()->id(),
            ]);
            $this->generatePdf($payroll);

            return redirect()->route('admin.payrolls.show', $payroll)
                ->with('success', 'Fiche de paie validée et PDF généré.');
        }

        return redirect()->route('admin.payrolls.edit', $payroll)
            ->with('success', 'Modifications enregistrées.');
    }

    public function downloadPdf(Payroll $payroll)
    {
        if (! $payroll->pdf_url || ! Storage::disk('public')->exists($payroll->pdf_url)) {
            $this->generatePdf($payroll);
        }

        return Storage::disk('public')->download($payroll->pdf_url);
    }

    public function markAsPaid(Payroll $payroll)
    {
        $payroll->update(['statut' => 'paid']);

        return back()->with('success', 'Marqué comme payé.');
    }

    private function generatePdf(Payroll $payroll): void
    {
        $payroll->load(['user.currentContract', 'items']);

        $pdf = Pdf::loadView('pdf.payroll-civ', [
            'payroll' => $payroll,
            'user' => $payroll->user,
            'contract' => $payroll->user->currentContract,
            'generatedAt' => now(),
        ]);

        $filename = "payrolls/bulletin_{$payroll->user->employee_id}_{$payroll->mois}_{$payroll->annee}.pdf";
        Storage::disk('public')->put($filename, $pdf->output());
        $payroll->update(['pdf_url' => $filename]);
    }
}
