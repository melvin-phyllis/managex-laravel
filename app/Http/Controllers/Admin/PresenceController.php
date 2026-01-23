<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Presence;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PresencesExport;

class PresenceController extends Controller
{
    public function index(Request $request)
    {
        $query = Presence::with('user');

        // Filtres
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_debut')) {
            $query->where('date', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->where('date', '<=', $request->date_fin);
        }

        if ($request->filled('mois')) {
            $date = Carbon::parse($request->mois);
            $query->month($date->month, $date->year);
        }

        $presences = $query->orderBy('date', 'desc')->paginate(15);
        $employees = User::where('role', 'employee')->orderBy('name')->get();

        return view('admin.presences.index', compact('presences', 'employees'));
    }

    public function exportCsv(Request $request)
    {
        $presences = $this->getFilteredPresences($request);

        $filename = 'presences_' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($presences) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Employé', 'Date', 'Arrivée', 'Départ', 'Heures travaillées']);

            foreach ($presences as $presence) {
                fputcsv($file, [
                    $presence->user->name,
                    $presence->date->format('d/m/Y'),
                    $presence->check_in_formatted,
                    $presence->check_out_formatted ?? '-',
                    $presence->hours_worked ? number_format($presence->hours_worked, 2) . 'h' : '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $presences = $this->getFilteredPresences($request);

        $pdf = Pdf::loadView('pdf.presences-report', [
            'presences' => $presences,
            'generatedAt' => now(),
        ]);

        return $pdf->download('presences_' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $filters = $request->only(['user_id', 'date_debut', 'date_fin']);
        return Excel::download(new PresencesExport($filters), 'presences_' . now()->format('Y-m-d') . '.xlsx');
    }

    private function getFilteredPresences(Request $request)
    {
        $query = Presence::with('user');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_debut')) {
            $query->where('date', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->where('date', '<=', $request->date_fin);
        }

        return $query->orderBy('date', 'desc')->get();
    }
}
