<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    /**
     * Afficher la liste des positions
     */
    public function index(Request $request)
    {
        $query = Position::with('department')
            ->withCount(['users' => function ($query) {
                $query->where('role', 'employee');
            }]);

        // Filtrer par département
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $positions = $query->orderBy('name')->paginate(10);
        $departments = Department::getActiveCached();

        return view('admin.positions.index', compact('positions', 'departments'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        $departments = Department::getActiveCached();

        return view('admin.positions.create', compact('departments'));
    }

    /**
     * Enregistrer une nouvelle position
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        // Vérifier unicité dans le département
        $exists = Position::where('department_id', $validated['department_id'])
            ->where('name', $validated['name'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'Cette position existe déjà dans ce département.'])->withInput();
        }

        $validated['is_active'] = $request->has('is_active');

        Position::create($validated);

        return redirect()->route('admin.positions.index')
            ->with('success', 'Position créée avec succès.');
    }

    /**
     * Afficher une position
     */
    public function show(Position $position)
    {
        $position->load(['department', 'users' => function ($query) {
            $query->where('role', 'employee');
        }]);

        return view('admin.positions.show', compact('position'));
    }

    /**
     * Formulaire d'édition
     */
    public function edit(Position $position)
    {
        $departments = Department::getActiveCached();

        return view('admin.positions.edit', compact('position', 'departments'));
    }

    /**
     * Mettre à jour une position
     */
    public function update(Request $request, Position $position)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        // Vérifier unicité dans le département (sauf pour cette position)
        $exists = Position::where('department_id', $validated['department_id'])
            ->where('name', $validated['name'])
            ->where('id', '!=', $position->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'Cette position existe déjà dans ce département.'])->withInput();
        }

        $validated['is_active'] = $request->has('is_active');

        $position->update($validated);

        return redirect()->route('admin.positions.index')
            ->with('success', 'Position mise à jour avec succès.');
    }

    /**
     * Supprimer une position
     */
    public function destroy(Position $position)
    {
        // Vérifier s'il y a des employés
        $employeesCount = $position->users()->where('role', 'employee')->count();

        if ($employeesCount > 0) {
            return redirect()->route('admin.positions.index')
                ->with('error', "Impossible de supprimer cette position car elle est assignée à {$employeesCount} employé(s).");
        }

        $position->delete();

        return redirect()->route('admin.positions.index')
            ->with('success', 'Position supprimée avec succès.');
    }
}
