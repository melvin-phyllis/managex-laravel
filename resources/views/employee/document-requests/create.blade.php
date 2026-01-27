<x-layouts.employee>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center gap-4">
            <a href="{{ route('employee.document-requests.index') }}" 
               class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📝 Nouvelle Demande de Document</h1>
                <p class="text-gray-500">Remplissez le formulaire ci-dessous</p>
            </div>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulaire -->
        <form action="{{ route('employee.document-requests.store') }}" method="POST">
            @csrf
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
                
                <!-- Type de document -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type de document *</label>
                    <select name="type" required
                            class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Sélectionnez un type</option>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Message / Motif -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Motif / Précisions</label>
                    <textarea name="message" rows="4" 
                              placeholder="Expliquez pourquoi vous avez besoin de ce document..."
                              class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">{{ old('message') }}</textarea>
                    <p class="text-sm text-gray-500 mt-1">Optionnel - Ajoutez des détails si nécessaire</p>
                </div>

                <!-- Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex gap-3">
                        <span class="text-xl">ℹ️</span>
                        <div>
                            <p class="text-sm text-blue-800 font-medium">Délai de traitement</p>
                            <p class="text-sm text-blue-700">Votre demande sera traitée dans un délai de 48h ouvrées. Vous recevrez une notification dès qu'elle sera prête.</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-4 pt-4 border-t">
                    <a href="{{ route('employee.document-requests.index') }}" 
                       class="px-6 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-emerald-600 text-white font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                        📤 Envoyer la demande
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.employee>
