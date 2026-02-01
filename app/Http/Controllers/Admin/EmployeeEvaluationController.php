<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeEvaluation;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EmployeeEvaluationController extends Controller
{
    /**
     * Afficher la liste des évaluations
     */
    public function index(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $evaluations = EmployeeEvaluation::with(['user', 'evaluator'])
            ->forPeriod($month, $year)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Employés CDI/CDD sans évaluation ce mois
        $evaluatedUserIds = EmployeeEvaluation::forPeriod($month, $year)->pluck('user_id');
        
        $pendingEmployees = User::where('role', 'employee')
            ->where('contract_type', '!=', 'stage')
            ->whereNotIn('id', $evaluatedUserIds)
            ->where('status', 'active')
            ->get();

        $smic = Setting::getSmicAmount();

        return view('admin.employee-evaluations.index', compact(
            'evaluations',
            'pendingEmployees',
            'month',
            'year',
            'smic'
        ));
    }

    /**
     * Formulaire de création d'une évaluation
     */
    public function create(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $userId = $request->get('user_id');

        // Récupérer les employés CDI/CDD qui n'ont pas encore été évalués ce mois
        $evaluatedUserIds = EmployeeEvaluation::forPeriod($month, $year)->pluck('user_id');
        
        $employees = User::where('role', 'employee')
            ->where('contract_type', '!=', 'stage')
            ->whereNotIn('id', $evaluatedUserIds)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $selectedEmployee = $userId ? User::find($userId) : null;
        $criteria = EmployeeEvaluation::CRITERIA;
        $smic = Setting::getSmicAmount();

        return view('admin.employee-evaluations.create', compact(
            'employees',
            'selectedEmployee',
            'month',
            'year',
            'criteria',
            'smic'
        ));
    }

    /**
     * Enregistrer une nouvelle évaluation
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2100',
            'problem_solving' => 'required|numeric|min:0|max:2',
            'objectives_respect' => 'required|numeric|min:0|max:0.5',
            'work_under_pressure' => 'required|numeric|min:0|max:1',
            'accountability' => 'required|numeric|min:0|max:2',
            'comments' => 'nullable|string|max:1000',
            'status' => 'nullable|in:draft,validated',
        ]);

        // Vérifier que l'employé n'est pas un stagiaire
        $user = User::findOrFail($validated['user_id']);
        if ($user->contract_type === 'stage') {
            return back()->withErrors(['user_id' => 'Les stagiaires ne peuvent pas être évalués avec ce système.']);
        }

        // Vérifier qu'il n'y a pas déjà une évaluation pour ce mois
        $existing = EmployeeEvaluation::where('user_id', $validated['user_id'])
            ->forPeriod($validated['month'], $validated['year'])
            ->exists();

        if ($existing) {
            return back()->withErrors(['user_id' => 'Une évaluation existe déjà pour cet employé ce mois.']);
        }

        $evaluation = new EmployeeEvaluation($validated);
        $evaluation->evaluated_by = auth()->id();
        
        if ($request->status === 'validated') {
            $evaluation->validated_at = now();
        }
        
        $evaluation->save();

        return redirect()->route('admin.employee-evaluations.index', [
            'month' => $validated['month'],
            'year' => $validated['year'],
        ])->with('success', 'Évaluation enregistrée avec succès.');
    }

    /**
     * Afficher une évaluation
     */
    public function show(EmployeeEvaluation $employeeEvaluation)
    {
        $employeeEvaluation->load(['user', 'evaluator']);
        $criteria = EmployeeEvaluation::CRITERIA;
        $smic = Setting::getSmicAmount();

        return view('admin.employee-evaluations.show', compact(
            'employeeEvaluation',
            'criteria',
            'smic'
        ));
    }

    /**
     * Formulaire d'édition
     */
    public function edit(EmployeeEvaluation $employeeEvaluation)
    {
        if (!$employeeEvaluation->canBeEdited()) {
            return redirect()->route('admin.employee-evaluations.show', $employeeEvaluation)
                ->with('error', 'Cette évaluation validée ne peut plus être modifiée.');
        }

        $employeeEvaluation->load('user');
        $criteria = EmployeeEvaluation::CRITERIA;
        $smic = Setting::getSmicAmount();

        return view('admin.employee-evaluations.edit', compact(
            'employeeEvaluation',
            'criteria',
            'smic'
        ));
    }

    /**
     * Mettre à jour une évaluation
     */
    public function update(Request $request, EmployeeEvaluation $employeeEvaluation)
    {
        if (!$employeeEvaluation->canBeEdited()) {
            return back()->with('error', 'Cette évaluation validée ne peut plus être modifiée.');
        }

        $validated = $request->validate([
            'problem_solving' => 'required|numeric|min:0|max:2',
            'objectives_respect' => 'required|numeric|min:0|max:0.5',
            'work_under_pressure' => 'required|numeric|min:0|max:1',
            'accountability' => 'required|numeric|min:0|max:2',
            'comments' => 'nullable|string|max:1000',
            'status' => 'nullable|in:draft,validated',
        ]);

        $employeeEvaluation->fill($validated);
        
        if ($request->status === 'validated' && $employeeEvaluation->status !== 'validated') {
            $employeeEvaluation->validated_at = now();
        }
        
        $employeeEvaluation->save();

        return redirect()->route('admin.employee-evaluations.index', [
            'month' => $employeeEvaluation->month,
            'year' => $employeeEvaluation->year,
        ])->with('success', 'Évaluation mise à jour avec succès.');
    }

    /**
     * Supprimer une évaluation
     */
    public function destroy(EmployeeEvaluation $employeeEvaluation)
    {
        if ($employeeEvaluation->status === 'validated') {
            return back()->with('error', 'Impossible de supprimer une évaluation validée.');
        }

        $month = $employeeEvaluation->month;
        $year = $employeeEvaluation->year;
        
        $employeeEvaluation->delete();

        return redirect()->route('admin.employee-evaluations.index', [
            'month' => $month,
            'year' => $year,
        ])->with('success', 'Évaluation supprimée.');
    }

    /**
     * Valider une évaluation
     */
    public function validate(EmployeeEvaluation $employeeEvaluation)
    {
        $employeeEvaluation->validate();

        return back()->with('success', 'Évaluation validée avec succès.');
    }

    /**
     * Évaluation rapide (bulk) - Afficher le formulaire
     */
    public function bulkCreate(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        // Employés sans évaluation ce mois
        $evaluatedUserIds = EmployeeEvaluation::forPeriod($month, $year)->pluck('user_id');
        
        $employees = User::where('role', 'employee')
            ->where('contract_type', '!=', 'stage')
            ->whereNotIn('id', $evaluatedUserIds)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $criteria = EmployeeEvaluation::CRITERIA;
        $smic = Setting::getSmicAmount();

        return view('admin.employee-evaluations.bulk-create', compact(
            'employees',
            'month',
            'year',
            'criteria',
            'smic'
        ));
    }

    /**
     * Enregistrer les évaluations en masse
     */
    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2100',
            'evaluations' => 'required|array',
            'evaluations.*.user_id' => 'required|exists:users,id',
            'evaluations.*.problem_solving' => 'required|numeric|min:0|max:2',
            'evaluations.*.objectives_respect' => 'required|numeric|min:0|max:0.5',
            'evaluations.*.work_under_pressure' => 'required|numeric|min:0|max:1',
            'evaluations.*.accountability' => 'required|numeric|min:0|max:2',
            'evaluations.*.comments' => 'nullable|string|max:500',
        ]);

        $count = 0;
        foreach ($validated['evaluations'] as $evalData) {
            // Vérifier si une évaluation existe déjà
            $existing = EmployeeEvaluation::where('user_id', $evalData['user_id'])
                ->forPeriod($validated['month'], $validated['year'])
                ->exists();

            if (!$existing) {
                $evaluation = new EmployeeEvaluation([
                    'user_id' => $evalData['user_id'],
                    'month' => $validated['month'],
                    'year' => $validated['year'],
                    'problem_solving' => $evalData['problem_solving'],
                    'objectives_respect' => $evalData['objectives_respect'],
                    'work_under_pressure' => $evalData['work_under_pressure'],
                    'accountability' => $evalData['accountability'],
                    'comments' => $evalData['comments'] ?? null,
                    'status' => 'validated',
                    'validated_at' => now(),
                ]);
                $evaluation->evaluated_by = auth()->id();
                $evaluation->save();
                $count++;
            }
        }

        return redirect()->route('admin.employee-evaluations.index', [
            'month' => $validated['month'],
            'year' => $validated['year'],
        ])->with('success', "{$count} évaluation(s) enregistrée(s) avec succès.");
    }

    /**
     * Calculer le salaire en temps réel (AJAX)
     */
    public function calculateSalary(Request $request)
    {
        $problemSolving = (float) $request->get('problem_solving', 0);
        $objectivesRespect = (float) $request->get('objectives_respect', 0);
        $workUnderPressure = (float) $request->get('work_under_pressure', 0);
        $accountability = (float) $request->get('accountability', 0);

        $totalScore = $problemSolving + $objectivesRespect + $workUnderPressure + $accountability;
        $totalScore = min($totalScore, EmployeeEvaluation::MAX_SCORE);

        $smic = Setting::getSmicAmount();
        $calculatedSalary = max($smic, $totalScore * $smic);

        return response()->json([
            'total_score' => $totalScore,
            'max_score' => EmployeeEvaluation::MAX_SCORE,
            'percentage' => round(($totalScore / EmployeeEvaluation::MAX_SCORE) * 100, 1),
            'calculated_salary' => $calculatedSalary,
            'formatted_salary' => number_format($calculatedSalary, 0, ',', ' ') . ' FCFA',
            'smic' => $smic,
        ]);
    }
}
