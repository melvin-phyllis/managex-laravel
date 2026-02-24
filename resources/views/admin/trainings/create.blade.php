<x-layouts.admin>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Nouvelle Formation</h1>
            <p class="text-sm text-gray-500 mt-1">Créez une formation et inscrivez des participants.</p>
        </div>
        <a href="{{ route('admin.trainings.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Retour</a>
    </div>

    <form action="{{ route('admin.trainings.store') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Titre *</label>
                <input type="text" name="title" value="{{ old('title') }}" required class="w-full rounded-xl border-gray-300 focus:border-[#1B3C35] focus:ring-[#1B3C35]">
                @error('title')<p class="text-sm text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                <select name="category" class="w-full rounded-xl border-gray-300 focus:border-[#1B3C35] focus:ring-[#1B3C35]">
                    <option value="">-- Choisir --</option>
                    @foreach(\App\Models\Training::CATEGORIES as $key => $label)
                        <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                <select name="type" required class="w-full rounded-xl border-gray-300 focus:border-[#1B3C35] focus:ring-[#1B3C35]">
                    @foreach(\App\Models\Training::TYPES as $key => $label)
                        <option value="{{ $key }}" {{ old('type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Durée (heures)</label>
                <input type="number" name="duration_hours" step="0.5" min="0.5" value="{{ old('duration_hours') }}" class="w-full rounded-xl border-gray-300 focus:border-[#1B3C35] focus:ring-[#1B3C35]">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Formateur</label>
                <input type="text" name="instructor" value="{{ old('instructor') }}" class="w-full rounded-xl border-gray-300 focus:border-[#1B3C35] focus:ring-[#1B3C35]">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Lieu</label>
                <input type="text" name="location" value="{{ old('location') }}" class="w-full rounded-xl border-gray-300 focus:border-[#1B3C35] focus:ring-[#1B3C35]">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nb max de participants</label>
                <input type="number" name="max_participants" min="1" value="{{ old('max_participants') }}" class="w-full rounded-xl border-gray-300 focus:border-[#1B3C35] focus:ring-[#1B3C35]">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select name="status" class="w-full rounded-xl border-gray-300 focus:border-[#1B3C35] focus:ring-[#1B3C35]">
                    <option value="draft">Brouillon</option>
                    <option value="published">Publié</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                <input type="date" name="start_date" value="{{ old('start_date') }}" class="w-full rounded-xl border-gray-300 focus:border-[#1B3C35] focus:ring-[#1B3C35]">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                <input type="date" name="end_date" value="{{ old('end_date') }}" class="w-full rounded-xl border-gray-300 focus:border-[#1B3C35] focus:ring-[#1B3C35]">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="3" class="w-full rounded-xl border-gray-300 focus:border-[#1B3C35] focus:ring-[#1B3C35]">{{ old('description') }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Inscrire des participants</label>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 max-h-48 overflow-y-auto border border-gray-200 rounded-xl p-3">
                @foreach($employees as $emp)
                    <label class="flex items-center gap-2 text-sm cursor-pointer hover:bg-gray-50 p-1 rounded-lg">
                        <input type="checkbox" name="participants[]" value="{{ $emp->id }}" class="rounded border-gray-300 text-[#1B3C35] focus:ring-[#1B3C35]">
                        {{ $emp->name }}
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="{{ route('admin.trainings.index') }}" class="px-4 py-2 text-sm font-medium rounded-xl border border-gray-200 text-gray-700 hover:bg-gray-50">Annuler</a>
            <button type="submit" class="px-6 py-2 text-sm font-medium text-white rounded-xl" style="background: linear-gradient(135deg, #1B3C35, #C8A96E);">Créer la formation</button>
        </div>
    </form>
</div>
</x-layouts.admin>
