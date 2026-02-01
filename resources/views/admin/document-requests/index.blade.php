<x-layouts.admin>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-fade-in-up">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📋 Demandes de Documents</h1>
                <p class="text-gray-500 mt-1">Gérez les demandes des employés</p>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 animate-fade-in-up animation-delay-100">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">⏳</span>
                    <div>
                        <p class="text-2xl font-bold text-amber-700">{{ $stats['pending'] }}</p>
                        <p class="text-sm text-amber-600">En attente</p>
                    </div>
                </div>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 animate-fade-in-up animation-delay-200">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">✅</span>
                    <div>
                        <p class="text-2xl font-bold text-green-700">{{ $stats['approved'] }}</p>
                        <p class="text-sm text-green-600">Approuvées</p>
                    </div>
                </div>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 animate-fade-in-up animation-delay-300">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">📊</span>
                    <div>
                        <p class="text-2xl font-bold text-blue-700">{{ $stats['total'] }}</p>
                        <p class="text-sm text-blue-600">Total</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 animate-fade-in-up animation-delay-200">
            <form method="GET" class="flex items-center gap-4">
                <select name="status" class="rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">En attente</option>
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
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
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employé</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Message</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($requests as $request)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-green-500 rounded-full flex items-center justify-center text-white font-bold">
                                            {{ strtoupper(substr($request->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $request->user->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $request->user->position->name ?? 'Non défini' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="font-medium text-gray-900">{{ $request->type_label }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="text-sm text-gray-600">{{ Str::limit($request->message ?? '-', 50) }}</p>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="text-sm text-gray-500">{{ $request->created_at->format('d/m/Y') }}</span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($request->status === 'approved') bg-green-100 text-green-700
                                        @elseif($request->status === 'rejected') bg-red-100 text-red-700
                                        @else bg-amber-100 text-amber-700 @endif">
                                        {{ $request->status_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    @if($request->isPending())
                                        <a href="{{ route('admin.document-requests.show', $request) }}" 
                                           class="inline-flex items-center px-3 py-1.5 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700">
                                            Traiter
                                        </a>
                                    @else
                                        <a href="{{ route('admin.document-requests.show', $request) }}" 
                                           class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200">
                                            Voir
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="px-4 py-3 border-t">
                    {{ $requests->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <span class="text-4xl">📭</span>
                    <p class="text-gray-500 mt-4">Aucune demande {{ request('status') ? '' : 'en attente' }}</p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
