<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use App\Models\UserSkill;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    /**
     * Mes compétences + radar chart.
     */
    public function index()
    {
        $user = auth()->user();

        $skills = Skill::orderBy('category')->orderBy('name')->get();

        $mySkills = UserSkill::where('user_id', $user->id)
            ->with(['skill', 'validator'])
            ->get()
            ->keyBy('skill_id');

        // Group skills by category for the radar chart
        $categories = $skills->groupBy('category');

        return view('employee.skills.index', compact('skills', 'mySkills', 'categories', 'user'));
    }

    /**
     * Auto-évaluation : ajouter ou modifier un niveau.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'skill_id' => 'required|exists:skills,id',
            'level' => 'required|integer|min:1|max:5',
        ]);

        UserSkill::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'skill_id' => $validated['skill_id'],
            ],
            [
                'level' => $validated['level'],
                'validated_by' => null,
                'validated_at' => null,
            ]
        );

        return back()->with('success', 'Compétence mise à jour.');
    }
}
