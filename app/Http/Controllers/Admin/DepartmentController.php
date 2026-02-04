<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Afficher la liste des départements
     */
    public function index()
    {
        $departments = Department::withCount(['users' => function ($query) {
            $query->where('role', 'employee');
        }, 'positions'])
            ->orderBy('name')
            ->paginate(10);

        return view('admin.departments.index', compact('departments'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        return view('admin.departments.create');
    }

    /**
     * Enregistrer un nouveau département
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'description' => 'nullable|string|max:1000',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Department::create($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Département créé avec succès.');
    }

    /**
     * Afficher un département
     */
    public function show(Department $department)
    {
        $department->load(['positions.users', 'users' => function ($query) {
            $query->where('role', 'employee');
        }]);

        return view('admin.departments.show', compact('department'));
    }

    /**
     * Formulaire d'édition
     */
    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    /**
     * Mettre à jour un département
     */
    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,'.$department->id,
            'description' => 'nullable|string|max:1000',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $department->update($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Département mis à jour avec succès.');
    }

    /**
     * Supprimer un département
     */
    public function destroy(Department $department)
    {
        // Vérifier s'il y a des employés
        $employeesCount = $department->users()->where('role', 'employee')->count();

        if ($employeesCount > 0) {
            return redirect()->route('admin.departments.index')
                ->with('error', "Impossible de supprimer ce département car il contient {$employeesCount} employé(s).");
        }

        $department->delete();

        return redirect()->route('admin.departments.index')
            ->with('success', 'Département supprimé avec succès.');
    }

    /**
     * API: Récupérer les positions d'un département (pour Select dynamique)
     */
    public function getPositions(Department $department)
    {
        $positions = $department->positions()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($positions);
    }
}
