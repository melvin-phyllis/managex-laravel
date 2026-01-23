<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = $user->payrolls();

        if ($request->filled('annee')) {
            $query->where('annee', $request->annee);
        }

        $payrolls = $query->orderBy('annee', 'desc')
                         ->orderBy('mois', 'desc')
                         ->paginate(12);

        // Années disponibles pour le filtre
        $years = $user->payrolls()
            ->distinct()
            ->orderBy('annee', 'desc')
            ->pluck('annee');

        // Statistiques
        $currentYear = now()->year;
        $stats = [
            'total_year' => $user->payrolls()
                ->where('annee', $currentYear)
                ->paid()
                ->sum('montant'),
            'pending' => $user->payrolls()->pending()->count(),
            'total_count' => $user->payrolls()->count(),
        ];

        return view('employee.payrolls.index', compact('payrolls', 'years', 'stats'));
    }

    public function show(Payroll $payroll)
    {
        $this->authorize('view', $payroll);
        return view('employee.payrolls.show', compact('payroll'));
    }

    public function downloadPdf(Payroll $payroll)
    {
        $this->authorize('view', $payroll);

        // Générer le PDF s'il n'existe pas
        if (!$payroll->pdf_url || !Storage::disk('public')->exists($payroll->pdf_url)) {
            $this->generatePdf($payroll);
        }

        return Storage::disk('public')->download(
            $payroll->pdf_url,
            "bulletin_paie_{$payroll->periode}.pdf"
        );
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
