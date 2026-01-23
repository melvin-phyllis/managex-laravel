<x-layouts.admin>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Détails de la demande</h1>
                <p class="text-gray-500 mt-1">Demande de {{ $leave->type_label }}</p>
            </div>
            <a href="{{ route('admin.leaves.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Leave Details -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-semibold text-gray-900">Informations de la demande</h2>
                        <x-status-badge :status="$leave->statut" type="leave" />
                    </div>

                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Type de congé</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ $leave->type_label }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Durée</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ $leave->duree }} jour(s)</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Date de début</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ $leave->date_debut->format('d/m/Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Date de fin</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ $leave->date_fin->format('d/m/Y') }}</dd>
                        </div>
                    </dl>

                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <dt class="text-sm font-medium text-gray-500 mb-2">Motif</dt>
                        <dd class="text-gray-900 bg-gray-50 p-4 rounded-lg">{{ $leave->motif ?? 'Aucun motif fourni.' }}</dd>
                    </div>

                    @if($leave->commentaire_admin)
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <dt class="text-sm font-medium text-gray-500 mb-2">Commentaire de l'administrateur</dt>
                            <dd class="text-gray-900 bg-blue-50 p-4 rounded-lg">{{ $leave->commentaire_admin }}</dd>
                        </div>
                    @endif
                </div>

                <!-- Actions -->
                @if($leave->statut === 'pending')
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Traiter la demande</h3>

                        <div class="mb-4">
                            <label for="commentaire_admin" class="block text-sm font-medium text-gray-700 mb-1">Commentaire (optionnel)</label>
                            <textarea id="commentaire_admin" name="commentaire_admin" rows="3" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Ajouter un commentaire..."></textarea>
                        </div>

                        <div class="flex items-center space-x-4">
                            <form action="{{ route('admin.leaves.approve', $leave) }}" method="POST" class="flex-1" id="approveForm">
                                @csrf
                                <input type="hidden" name="commentaire_admin" id="approveComment">
                                <button type="submit" class="w-full px-4 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Approuver
                                </button>
                            </form>
                            <form action="{{ route('admin.leaves.reject', $leave) }}" method="POST" class="flex-1" id="rejectForm">
                                @csrf
                                <input type="hidden" name="commentaire_admin" id="rejectComment">
                                <button type="submit" class="w-full px-4 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Rejeter
                                </button>
                            </form>
                        </div>
                    </div>

                    <script>
                        document.getElementById('approveForm').addEventListener('submit', function() {
                            document.getElementById('approveComment').value = document.getElementById('commentaire_admin').value;
                        });
                        document.getElementById('rejectForm').addEventListener('submit', function() {
                            document.getElementById('rejectComment').value = document.getElementById('commentaire_admin').value;
                        });
                    </script>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Employee Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Employé</h3>
                    <div class="flex items-center">
                        @if($leave->user->avatar)
                            <img src="{{ Storage::url($leave->user->avatar) }}" alt="{{ $leave->user->name }}" class="w-12 h-12 rounded-full object-cover">
                        @else
                            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                                <span class="text-blue-600 font-medium">{{ strtoupper(substr($leave->user->name, 0, 2)) }}</span>
                            </div>
                        @endif
                        <div class="ml-3">
                            <p class="font-medium text-gray-900">{{ $leave->user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $leave->user->poste ?? 'Non défini' }}</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.employees.show', $leave->user) }}" class="mt-4 block text-center text-sm text-blue-600 hover:text-blue-800">
                        Voir le profil complet →
                    </a>
                </div>

                <!-- Request Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Historique</h3>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm text-gray-500">Demande créée le</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900">{{ $leave->created_at->format('d/m/Y à H:i') }}</dd>
                        </div>
                        @if($leave->updated_at != $leave->created_at)
                            <div>
                                <dt class="text-sm text-gray-500">Dernière modification</dt>
                                <dd class="mt-1 text-sm font-medium text-gray-900">{{ $leave->updated_at->format('d/m/Y à H:i') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                <!-- Leave History -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Autres congés de l'employé</h3>
                    <div class="space-y-3">
                        @forelse($leave->user->leaves()->where('id', '!=', $leave->id)->latest()->take(3)->get() as $otherLeave)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">{{ $otherLeave->type_label }}</span>
                                <x-status-badge :status="$otherLeave->statut" type="leave" />
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Aucun autre congé</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
