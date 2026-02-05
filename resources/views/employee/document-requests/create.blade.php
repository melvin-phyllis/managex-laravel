<x-layouts.employee>
    <div class="space-y-6">
        <!-- Header avec style premium -->
        <div class="rounded-2xl p-6 text-white shadow-xl" style="background-color: #3B8BEB;">
            <div class="flex items-center gap-4">
                <a href="{{ route('employee.document-requests.index') }}" 
                   class="p-2 rounded-xl bg-white/20 hover:bg-white/30 transition-colors">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold">Nouvelle Demande de Document</h1>
                    <p style="color: #C4DBF6;">Remplissez le formulaire ci-dessous pour demander un document</p>
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="rounded-xl p-4 flex items-start gap-3" style="background-color: rgba(178, 56, 80, 0.1); border: 1px solid rgba(178, 56, 80, 0.2);">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: #B23850;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="font-medium" style="color: #B23850;">Veuillez corriger les erreurs suivantes :</p>
                    <ul class="list-disc list-inside mt-1 text-sm" style="color: #B23850;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Formulaire Principal -->
        <form action="{{ route('employee.document-requests.store') }}" method="POST">
            @csrf
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- En-tête du formulaire -->
                <div class="px-6 py-4 border-b border-gray-100" style="background-color: rgba(59, 139, 235, 0.03);">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #3B8BEB;">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-gray-900">Informations de la demande</h2>
                            <p class="text-sm text-gray-500">Sélectionnez le type de document souhaité</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Type de document -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Type de document <span style="color: #B23850;">*</span>
                        </label>
                        <div class="relative">
                            <select name="type" required
                                    class="w-full rounded-xl border-gray-200 py-3 pl-4 pr-10 focus:border-[#3B8BEB] focus:ring-[#3B8BEB] appearance-none bg-gray-50 hover:bg-gray-100 transition-colors">
                                <option value="">Sélectionnez un type de document</option>
                                @foreach($types as $key => $label)
                                    <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Motif / Précisions -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Motif / Précisions
                            <span class="text-gray-400 font-normal">(optionnel)</span>
                        </label>
                        <textarea name="message" rows="4" 
                                  placeholder="Décrivez pourquoi vous avez besoin de ce document, ajoutez des détails si nécessaire..."
                                  class="w-full rounded-xl border-gray-200 py-3 px-4 focus:border-[#3B8BEB] focus:ring-[#3B8BEB] bg-gray-50 hover:bg-gray-100 transition-colors resize-none">{{ old('message') }}</textarea>
                    </div>

                    <!-- Zone d'information -->
                    <div class="rounded-xl p-4" style="background-color: rgba(59, 139, 235, 0.08);">
                        <div class="flex gap-4">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background-color: #3B8BEB;">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium" style="color: #3B8BEB;">Délai de traitement</p>
                                <p class="text-sm text-gray-600 mt-1">
                                    Votre demande sera traitée dans un délai de <strong>48 heures ouvrées</strong>. 
                                    Vous recevrez une notification dès que votre document sera prêt à télécharger.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-end gap-3" style="background-color: #f9fafb;">
                    <a href="{{ route('employee.document-requests.index') }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Annuler
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center gap-2 px-5 py-2.5 text-white font-medium rounded-xl transition-all shadow-lg hover:shadow-xl" 
                            style="background-color: #3B8BEB; box-shadow: 0 10px 15px -3px rgba(59, 139, 235, 0.3);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Envoyer la demande
                    </button>
                </div>
            </div>
        </form>

        <!-- Types de documents disponibles -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5" style="color: #8590AA;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Types de documents disponibles
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($types as $key => $label)
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(59, 139, 235, 0.1);">
                            <svg class="w-4 h-4" style="color: #3B8BEB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <span class="text-sm text-gray-700">{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.employee>
