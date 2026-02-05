<x-layouts.admin>
    <div class="space-y-6" x-data="payrollTable()">
        <!-- Header comme sur tasks -->
        <div class="relative overflow-hidden rounded-2xl shadow-xl animate-fade-in-up" style="background: linear-gradient(135deg, #5680E9, #84CEEB) !important;">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            
            <div class="relative p-6 md:p-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <nav class="flex mb-3" aria-label="Breadcrumb">
                            <ol class="inline-flex items-center space-x-1">
                                <li><a href="{{ route('admin.dashboard') }}" class="text-white/70 hover:text-white text-sm">Dashboard</a></li>
                                <li><span class="text-white/50 mx-2">/</span></li>
                                <li><span class="text-white text-sm font-medium">Fiches de paie</span></li>
                            </ol>
                        </nav>
                        <h1 class="text-2xl md:text-3xl font-bold text-white flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            Gestion des fiches de paie
                        </h1>
                        <p class="text-white/80 mt-2">Génération et suivi des bulletins de salaire</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <button @click="showBulkModal = true" class="px-4 py-2.5 bg-white/20 backdrop-blur-sm text-white font-semibold rounded-xl hover:bg-white/30 transition-all flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
                            Générer en masse
                        </button>
                        <a href="{{ route('admin.payrolls.create') }}" class="px-4 py-2.5 bg-white font-semibold rounded-xl hover:bg-purple-50 transition-all shadow-lg flex items-center" style="color: #5680E9;">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Nouvelle fiche
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100 animate-fade-in-up animation-delay-100">
            <form action="{{ route('admin.payrolls.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">Employé</label>
                    <select name="employee_id" id="employee_id" class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Tous les employés</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="mois" class="block text-sm font-medium text-gray-700 mb-1">Mois</label>
                    <select name="mois" id="mois" class="rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Tous les mois</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ request('mois') == $i ? 'selected' : '' }}>{{ ucfirst(\Carbon\Carbon::create()->month($i)->translatedFormat('F')) }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label for="annee" class="block text-sm font-medium text-gray-700 mb-1">Année</label>
                    <select name="annee" id="annee" class="rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Toutes</option>
                        @for($i = now()->year; $i >= now()->year - 5; $i--)
                            <option value="{{ $i }}" {{ request('annee') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select name="statut" id="statut" class="rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Tous</option>
                        <option value="paid" {{ request('statut') == 'paid' ? 'selected' : '' }}>Payé</option>
                        <option value="pending" {{ request('statut') == 'pending' ? 'selected' : '' }}>En attente</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="px-6 py-2.5 text-white font-medium rounded-xl transition-colors shadow-lg flex items-center" style="background: linear-gradient(135deg, #5680E9, #5AB9EA);">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Filtrer
                    </button>
                </div>
            </form>
        </div>

        <!-- Payrolls Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in-up animation-delay-200">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Employé</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Période</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Montant Net</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($payrolls as $payroll)
                            <tr class="hover:bg-purple-50/50 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        @if($payroll->user->avatar)
                                            <img class="h-9 w-9 rounded-full object-cover ring-2 ring-white shadow-sm" src="{{ avatar_url($payroll->user->avatar) }}" alt="{{ $payroll->user->name }}">
                                        @else
                                            <div class="h-9 w-9 rounded-full flex items-center justify-center ring-2 ring-white shadow-sm" style="background: linear-gradient(135deg, #5680E9, #84CEEB);">
                                                <span class="text-white font-bold text-xs">{{ strtoupper(substr($payroll->user->name, 0, 2)) }}</span>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $payroll->user->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium" style="color: #5680E9;">{{ $payroll->periode }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">{{ $payroll->net_salary_formatted ?? $payroll->montant_formatted }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($payroll->statut === 'paid')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium" style="background-color: rgba(90, 185, 234, 0.15); color: #5680E9;">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            Payé
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium" style="background-color: rgba(136, 96, 208, 0.15); color: #8860D0;">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            En attente
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="{{ route('admin.payrolls.download', $payroll) }}" class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Télécharger PDF">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                        </a>
                                        @if($payroll->statut === 'pending')
                                            <form action="{{ route('admin.payrolls.mark-paid', $payroll) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="p-1.5 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Marquer comme payé">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('admin.payrolls.edit', $payroll) }}" class="p-1.5 text-gray-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Modifier">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <button type="button" 
                                                @click="confirmDelete('{{ route('admin.payrolls.destroy', $payroll) }}')"
                                                class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" 
                                                title="Supprimer">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                        </div>
                                        <p class="text-lg font-medium text-gray-900">Aucune fiche de paie trouvée</p>
                                        <p class="text-sm text-gray-500 mt-1">Modifiez vos filtres ou créez une nouvelle fiche.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($payrolls->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $payrolls->links() }}
                </div>
            @endif
        </div>

        <!-- Bulk Generate Modal -->
        <div x-show="showBulkModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showBulkModal = false">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form action="{{ route('admin.payrolls.bulk-generate') }}" method="POST">
                        @csrf
                        <div class="bg-white px-6 pt-6 pb-4">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-xl" style="background-color: rgba(136, 96, 208, 0.15);">
                                    <svg class="h-6 w-6" style="color: #8860D0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                    </svg>
                                </div>
                                <div class="w-full">
                                    <h3 class="text-lg leading-6 font-bold text-gray-900">
                                        Génération de masse
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">
                                            Cela générera les fiches de paie pour tous les employés actifs ayant un contrat valide pour la période sélectionnée.
                                        </p>
                                        <div class="mt-4 grid grid-cols-2 gap-4">
                                            <div>
                                                <label for="bulk_mois" class="block text-sm font-medium text-gray-700 mb-1">Mois</label>
                                                <select name="mois" id="bulk_mois" class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                                                    @for($i = 1; $i <= 12; $i++)
                                                        <option value="{{ $i }}" {{ now()->month == $i ? 'selected' : '' }}>{{ ucfirst(\Carbon\Carbon::create()->month($i)->translatedFormat('F')) }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div>
                                                <label for="bulk_annee" class="block text-sm font-medium text-gray-700 mb-1">Année</label>
                                                <select name="annee" id="bulk_annee" class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                                                    @for($i = now()->year; $i >= now()->year - 2; $i--)
                                                        <option value="{{ $i }}" {{ now()->year == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                            <button type="button" @click="showBulkModal = false" class="px-4 py-2.5 bg-white text-gray-700 font-medium rounded-xl border border-gray-300 hover:bg-gray-50 transition-colors">
                                Annuler
                            </button>
                            <button type="submit" class="px-4 py-2.5 text-white font-semibold rounded-xl shadow-lg transition-all" style="background: linear-gradient(135deg, #8860D0, #5680E9);">
                                Lancer la génération
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <!-- Delete Confirmation Modal -->
        <div x-show="showDeleteModal" 
             class="fixed inset-0 z-[100] overflow-y-auto" 
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true"
             style="display: none;">
            
            <!-- Backdrop -->
            <div x-show="showDeleteModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                 @click="showDeleteModal = false"></div>

            <div class="flex min-h-screen items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="showDeleteModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Confirmer la suppression</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">Êtes-vous sûr de vouloir supprimer cette fiche de paie ? Cette action est irréversible.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <form :action="deleteUrl" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="inline-flex w-full justify-center rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">
                                Supprimer
                            </button>
                        </form>
                        <button type="button" 
                                @click="showDeleteModal = false"
                                class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                            Annuler
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script nonce="{{ $cspNonce ?? '' }}">
        function payrollTable() {
            return {
                showBulkModal: false,
                showDeleteModal: false,
                deleteUrl: '',
                confirmDelete(url) {
                    this.deleteUrl = url;
                    this.showDeleteModal = true;
                }
            }
        }
    </script>
</x-layouts.admin>