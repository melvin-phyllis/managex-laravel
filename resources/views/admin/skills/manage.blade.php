<x-layouts.admin>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestion des Compétences</h1>
            <p class="text-sm text-gray-500 mt-1">Ajoutez et organisez les compétences de l'entreprise.</p>
        </div>
        <a href="{{ route('admin.skills.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Voir la matrice →</a>
    </div>

    @if(session('success'))
        <div class="border-l-4 p-4 rounded-r-lg" style="background: rgba(27, 60, 53, 0.1); border-color: #1B3C35;">
            <p class="text-sm font-medium" style="color: #1B3C35;">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Add skill form --}}
    <form action="{{ route('admin.skills.store') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        @csrf
        <h3 class="font-semibold text-gray-900 mb-4">Ajouter une compétence</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <input type="text" name="name" placeholder="Nom de la compétence" required class="w-full rounded-xl border-gray-300 focus:border-[#1B3C35] focus:ring-[#1B3C35]">
                @error('name')<p class="text-sm text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <select name="category" required class="w-full rounded-xl border-gray-300 focus:border-[#1B3C35] focus:ring-[#1B3C35]">
                    <option value="">-- Catégorie --</option>
                    @foreach(\App\Models\Skill::CATEGORIES as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <input type="text" name="description" placeholder="Description (optionnel)" class="flex-1 rounded-xl border-gray-300 focus:border-[#1B3C35] focus:ring-[#1B3C35]">
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white rounded-xl" style="background: #1B3C35;">Ajouter</button>
            </div>
        </div>
    </form>

    {{-- Skills list by category --}}
    @foreach($skills->groupBy('category') as $category => $categorySkills)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-3 border-b border-gray-100" style="background: linear-gradient(90deg, rgba(27,60,53,0.1), rgba(133,144,170,0.08));">
                <h3 class="font-semibold text-gray-900 text-sm">{{ \App\Models\Skill::CATEGORIES[$category] ?? $category }} ({{ $categorySkills->count() }})</h3>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($categorySkills as $skill)
                    <div class="p-4 flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-900 text-sm">{{ $skill->name }}</p>
                            @if($skill->description)<p class="text-xs text-gray-400">{{ $skill->description }}</p>@endif
                        </div>
                        <form action="{{ route('admin.skills.destroy', $skill) }}" method="POST" onsubmit="return confirm('Supprimer cette compétence ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Supprimer</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

    @if($skills->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
            <p class="text-gray-500">Aucune compétence définie. Utilisez le formulaire ci-dessus pour en ajouter.</p>
        </div>
    @endif
</div>
</x-layouts.admin>
