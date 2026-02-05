<x-layouts.employee>
    <div class="space-y-6">
        <!-- Header avec Tolia Blue -->
        <div class="relative overflow-hidden rounded-2xl p-6 text-white shadow-xl" style="background-color: #3B8BEB;">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full" style="transform: translate(30%, -50%);"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full" style="transform: translate(-30%, 50%);"></div>
            
            <div class="relative flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold mb-1">Mes demandes de congés</h1>
                    <p style="color: #C4DBF6;">Gérez vos demandes de congés et absences</p>
                </div>
                <a href="{{ route('employee.leaves.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white/20 backdrop-blur-sm text-white font-medium rounded-xl hover:bg-white/30 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Nouvelle demande
                </a>
            </div>
        </div>

        <!-- Stats Summary -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: #3B8BEB;">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                        <p class="text-sm text-gray-500">Total demandes</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: #E7E3D4;">
                        <svg class="w-6 h-6" style="color: #8590AA;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold" style="color: #8590AA;">{{ $stats['pending'] ?? 0 }}</p>
                        <p class="text-sm text-gray-500">En attente</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: rgba(59, 139, 235, 0.15);">
                        <svg class="w-6 h-6" style="color: #3B8BEB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold" style="color: #3B8BEB;">{{ $stats['approved'] ?? 0 }}</p>
                        <p class="text-sm text-gray-500">Approuvées</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: rgba(178, 56, 80, 0.15);">
                        <svg class="w-6 h-6" style="color: #B23850;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold" style="color: #B23850;">{{ $stats['rejected'] ?? 0 }}</p>
                        <p class="text-sm text-gray-500">Rejetées</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leaves Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100" style="background-color: rgba(231, 227, 212, 0.3);">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5" style="color: #3B8BEB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Historique des demandes
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Période</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Durée</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Motif</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Réponse Admin</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($leaves as $leave)
                            @php
                                $typeConfig = [
                                    'conge' => ['bg' => 'rgba(59, 139, 235, 0.15)', 'text' => '#3B8BEB'],
                                    'maladie' => ['bg' => 'rgba(178, 56, 80, 0.15)', 'text' => '#B23850'],
                                    'autre' => ['bg' => '#E7E3D4', 'text' => '#8590AA'],
                                ];
                                $type = $typeConfig[$leave->type] ?? $typeConfig['autre'];
                                
                                $statusConfig = [
                                    'pending' => ['bg' => '#E7E3D4', 'text' => '#8590AA', 'label' => 'En cours'],
                                    'approved' => ['bg' => 'rgba(59, 139, 235, 0.15)', 'text' => '#3B8BEB', 'label' => 'Approuvé'],
                                    'rejected' => ['bg' => 'rgba(178, 56, 80, 0.15)', 'text' => '#B23850', 'label' => 'Rejeté'],
                                ];
                                $status = $statusConfig[$leave->statut] ?? $statusConfig['pending'];
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="background-color: {{ $type['bg'] }}; color: {{ $type['text'] }};">
                                        {{ $leave->type_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $leave->date_debut->format('d/m/Y') }}</div>
                                    <div class="text-sm text-gray-500">au {{ $leave->date_fin->format('d/m/Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $leave->duree }} jour(s)</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500 max-w-xs truncate">{{ Str::limit($leave->motif, 40) ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="background-color: {{ $status['bg'] }}; color: {{ $status['text'] }};">
                                        {{ $status['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($leave->statut !== 'pending')
                                        @if($leave->commentaire_admin)
                                            <div x-data="{ showModal: false }">
                                                <button @click="showModal = true" class="inline-flex items-center text-sm font-medium transition-colors" style="color: #3B8BEB;">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                                    </svg>
                                                    Voir commentaire
                                                </button>

                                                <!-- Modal -->
                                                <div x-show="showModal" x-cloak
                                                    class="fixed inset-0 z-50 overflow-y-auto"
                                                    x-transition:enter="transition ease-out duration-300"
                                                    x-transition:enter-start="opacity-0"
                                                    x-transition:enter-end="opacity-100"
                                                    x-transition:leave="transition ease-in duration-200"
                                                    x-transition:leave-start="opacity-100"
                                                    x-transition:leave-end="opacity-0">
                                                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                                                        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showModal = false"></div>

                                                        <div class="relative z-10 w-full max-w-md p-6 mx-auto bg-white rounded-2xl shadow-xl"
                                                            x-transition:enter="transition ease-out duration-300"
                                                            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">

                                                            <div class="flex items-center justify-between mb-4">
                                                                <h3 class="text-lg font-semibold text-gray-900">
                                                                    Commentaire de l'administration
                                                                </h3>
                                                                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                    </svg>
                                                                </button>
                                                            </div>

                                                            <div class="mb-4">
                                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium" 
                                                                      style="background-color: {{ $leave->statut === 'approved' ? 'rgba(59, 139, 235, 0.15)' : 'rgba(178, 56, 80, 0.15)' }}; color: {{ $leave->statut === 'approved' ? '#3B8BEB' : '#B23850' }};">
                                                                    {{ $leave->statut === 'approved' ? '✓ Approuvé' : '✗ Refusé' }}
                                                                </span>
                                                            </div>

                                                            <div class="p-4 rounded-xl" style="background-color: rgba(231, 227, 212, 0.5);">
                                                                <p class="text-sm text-gray-700">{{ $leave->commentaire_admin }}</p>
                                                            </div>

                                                            <div class="mt-4">
                                                                <button @click="showModal = false" class="w-full px-4 py-2.5 text-sm font-medium text-white rounded-xl transition-colors" style="background-color: #3B8BEB;">
                                                                    Fermer
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400 italic">Aucun commentaire</span>
                                        @endif
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if($leave->statut === 'pending')
                                        <form action="{{ route('employee.leaves.destroy', $leave) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette demande ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-medium transition-colors" style="color: #B23850;">Annuler</button>
                                        </form>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <div class="w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background-color: rgba(59, 139, 235, 0.1);">
                                        <svg class="w-10 h-10" style="color: #3B8BEB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucune demande de congé</h3>
                                    <p class="text-gray-500 mb-4">Vous n'avez pas encore fait de demande de congé.</p>
                                    <a href="{{ route('employee.leaves.create') }}" 
                                       class="inline-flex items-center gap-2 px-4 py-2 text-white font-medium rounded-xl transition-colors" style="background-color: #3B8BEB;">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Faire une demande
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($leaves->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $leaves->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.employee>
