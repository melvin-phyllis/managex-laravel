<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use App\Models\User;
use App\Models\UserSkill;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    /**
     * Matrice de compétences globale.
     */
    public function index(Request $request)
    {
        $skills = Skill::orderBy('category')->orderBy('name')->get();

        $employees = User::where('role', 'employee')
            ->where('status', 'active')
            ->with(['userSkills.skill'])
            ->orderBy('name')
            ->get();

        if ($request->filled('category')) {
            $skills = $skills->where('category', $request->category);
        }

        return view('admin.skills.index', compact('skills', 'employees'));
    }

    /**
     * Gérer les compétences (CRUD).
     */
    public function manage()
    {
        $skills = Skill::orderBy('category')->orderBy('name')->get();
        return view('admin.skills.manage', compact('skills'));
    }

    /**
     * Créer une compétence.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:skills,name',
            'category' => 'required|string|max:50',
            'description' => 'nullable|string|max:500',
        ]);

        Skill::create($validated);

        return back()->with('success', 'Compétence ajoutée.');
    }

    /**
     * Supprimer une compétence.
     */
    public function destroy(Skill $skill)
    {
        $skill->delete();
        return back()->with('success', 'Compétence supprimée.');
    }

    /**
     * Valider le niveau d'un employé.
     */
    public function validateLevel(Request $request, UserSkill $userSkill)
    {
        $userSkill->update([
            'validated_by' => auth()->id(),
            'validated_at' => now(),
        ]);

        return back()->with('success', 'Niveau validé.');
    }
}
