<x-layouts.admin>
    <div class="space-y-6">
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
                                <li><a href="{{ route('admin.documents.index') }}" class="text-white/70 hover:text-white text-sm">Documents</a></li>
                                <li><span class="text-white/50 mx-2">/</span></li>
                                <li><span class="text-white text-sm font-medium">Demandes</span></li>
                            </ol>
                        </nav>
                        <h1 class="text-2xl md:text-3xl font-bold text-white flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                </svg>
                            </div>
                            Demandes de Documents
                        </h1>
                        <p class="text-white/80 mt-2">Gérez les demandes des employés</p>
                    </div>
                    <a href="{{ route('admin.documents.index') }}" 
                       class="px-4 py-2.5 bg-white font-semibold rounded-xl hover:bg-purple-50 transition-all shadow-lg flex items-center" style="color: #5680E9;">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Retour
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3 animate-fade-in-up">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <p class="text-green-800 font-medium">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 animate-fade-in-up animation-delay-100">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: rgba(245, 158, 11, 0.15);">
                        <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-amber-500">{{ $stats['pending'] }}</p>
                        <p class="text-sm text-gray-500">En attente</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: rgba(90, 185, 234, 0.15);">
                        <svg class="w-6 h-6" style="color: #5AB9EA;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold" style="color: #5AB9EA;">{{ $stats['approved'] }}</p>
                        <p class="text-sm text-gray-500">Approuvées</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: rgba(86, 128, 233, 0.15);">
                        <svg class="w-6 h-6" style="color: #5680E9;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold" style="color: #5680E9;">{{ $stats['total'] }}</p>
                        <p class="text-sm text-gray-500">Total</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 animate-fade-in-up animation-delay-200">
            <form method="GET" class="flex items-center gap-4">
                <select name="status" class="rounded-xl border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">En attente</option>
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors">
                    Filtrer
                </button>
                @if(request('status'))
                    <a href="{{ route('admin.document-requests.index') }}" class="text-gray-500 hover:text-gray-700">
                        Réinitialiser
                    </a>
                @endif
            </form>
        </div>

        <!-- Liste des demandes -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in-up animation-delay-300">
            @if($requests->count() > 0)
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Employé</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Message</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Date</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Statut</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($requests as $request)
                            <tr class="hover:bg-purple-50/50 transition-colors group">
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold" style="background: linear-gradient(135deg, #5680E9, #84CEEB);">
                                            {{ strtoupper(substr($request->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $request->user->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $request->user->position->name ?? 'Non défini' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full" style="background-color: rgba(136, 96, 208, 0.15); color: #8860D0;">
                                        {{ $request->type_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="text-sm text-gray-600">{{ Str::limit($request->message ?? '-', 50) }}</p>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="text-sm text-gray-500">{{ $request->created_at->format('d/m/Y') }}</span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if($request->status === 'approved')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full" style="background-color: rgba(90, 185, 234, 0.15); color: #5AB9EA;">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            {{ $request->status_label }}
                                        </span>
                                    @elseif($request->status === 'rejected')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full bg-red-50 text-red-600">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            {{ $request->status_label }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full bg-amber-50 text-amber-600">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $request->status_label }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-right">
                                    @if($request->isPending())
                                        <a href="{{ route('admin.document-requests.show', $request) }}" 
                                           class="inline-flex items-center px-3 py-1.5 text-white text-sm font-medium rounded-xl shadow-lg transition-all" style="background: linear-gradient(135deg, #5680E9, #5AB9EA);">
                                            Traiter
                                        </a>
                                    @else
                                        <a href="{{ route('admin.document-requests.show', $request) }}" 
                                           class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition-colors">
                                            Voir
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="px-4 py-3 border-t border-gray-100">
                    {{ $requests->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background-color: rgba(86, 128, 233, 0.15);">
                        <svg class="w-8 h-8" style="color: #5680E9;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                    </div>
                    <p class="text-lg font-medium text-gray-900">Aucune demande</p>
                    <p class="text-gray-500 mt-1">{{ request('status') ? 'pour ce filtre' : 'en attente' }}</p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
