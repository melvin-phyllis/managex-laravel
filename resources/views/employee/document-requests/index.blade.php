<x-layouts.employee>
    <div class="space-y-4">
        <!-- Header avec gradient -->
        <div class="rounded-2xl p-6 text-white shadow-xl" style="background-color: #1B3C35;">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold mb-1">Mes Demandes de Documents</h1>
                    <p style="color: #C4DBF6;">Demandez une attestation, un certificat ou tout autre document</p>
                </div>
                <a href="{{ route('employee.document-requests.create') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-white font-semibold rounded-lg hover:bg-gray-50 transition-colors shadow-sm" style="color: #1B3C35;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nouvelle demande
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        @php
            $totalRequests = $requests->count();
            $pendingCount = $requests->where('status', 'pending')->count();
            $approvedCount = $requests->where('status', 'approved')->count();
            $rejectedCount = $requests->where('status', 'rejected')->count();
        @endphp
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #1B3C35;">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalRequests }}</p>
                        <p class="text-xs text-gray-500">Total demandes</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #E7E3D4;">
                        <svg class="w-5 h-5" style="color: #5C6E68;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold" style="color: #5C6E68;">{{ $pendingCount }}</p>
                        <p class="text-xs text-gray-500">En attente</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: rgba(27, 60, 53, 0.15);">
                        <svg class="w-5 h-5" style="color: #1B3C35;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold" style="color: #1B3C35;">{{ $approvedCount }}</p>
                        <p class="text-xs text-gray-500">Approuvées</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: rgba(200, 169, 110, 0.15);">
                        <svg class="w-5 h-5" style="color: #C8A96E;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold" style="color: #C8A96E;">{{ $rejectedCount }}</p>
                        <p class="text-xs text-gray-500">Refusées</p>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="px-4 py-3 rounded-xl flex items-center gap-3" style="background-color: rgba(27, 60, 53, 0.1); border: 1px solid rgba(27, 60, 53, 0.2); color: #1B3C35;">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- Liste des demandes -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100" style="background-color: rgba(27, 60, 53, 0.03);">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5" style="color: #1B3C35;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Historique des demandes
                </h3>
            </div>

            @if($requests->count() > 0)
                <div class="divide-y divide-gray-100">
                    @foreach($requests as $request)
                        @php
                            $statusConfig = [
                                'pending' => ['bg' => 'background-color: rgba(133, 144, 170, 0.15);', 'text' => 'color: #5C6E68;', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'iconBg' => '#E7E3D4', 'iconColor' => '#5C6E68'],
                                'approved' => ['bg' => 'background-color: rgba(27, 60, 53, 0.1);', 'text' => 'color: #1B3C35;', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'iconBg' => '#1B3C35', 'iconColor' => '#fff'],
                                'rejected' => ['bg' => 'background-color: rgba(200, 169, 110, 0.1);', 'text' => 'color: #C8A96E;', 'icon' => 'M6 18L18 6M6 6l12 12', 'iconBg' => '#C8A96E', 'iconColor' => '#fff'],
                            ];
                            $status = $statusConfig[$request->status] ?? $statusConfig['pending'];
                        @endphp
                        <div class="p-5 hover:bg-gray-50 transition-colors">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background-color: {{ $status['iconBg'] }};">
                                        <svg class="w-6 h-6" style="color: {{ $status['iconColor'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $status['icon'] }}"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $request->type_label }}</p>
                                        <p class="text-sm text-gray-500 mt-0.5">
                                            Demandé le {{ $request->created_at->format('d/m/Y à H:i') }}
                                        </p>
                                        @if($request->message)
                                            <p class="text-sm text-gray-600 mt-2 italic">« {{ Str::limit($request->message, 100) }} »</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 ml-16 sm:ml-0">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-full" style="{{ $status['bg'] }} {{ $status['text'] }}">
                                        {{ $request->status_label }}
                                    </span>
                                    @if($request->hasDocument())
                                        <a href="{{ route('employee.document-requests.download', $request) }}" 
                                           class="inline-flex items-center gap-2 px-4 py-2 text-white text-sm font-medium rounded-lg transition-colors shadow-sm" style="background-color: #1B3C35;">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                            Télécharger
                                        </a>
                                    @endif
                                </div>
                            </div>
                            @if($request->admin_response)
                                <div class="mt-4 ml-16 p-4 bg-gray-50 rounded-xl border border-gray-100">
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">Réponse RH</p>
                                            <p class="text-sm text-gray-600 mt-1">{{ $request->admin_response }}</p>
                                            @if($request->responded_at)
                                                <p class="text-xs text-gray-400 mt-2">{{ $request->responded_at->format('d/m/Y à H:i') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background-color: rgba(27, 60, 53, 0.1);">
                        <svg class="w-10 h-10" style="color: #1B3C35;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucune demande</h3>
                    <p class="text-gray-500 mb-4">Vous n'avez pas encore fait de demande de document</p>
                    <a href="{{ route('employee.document-requests.create') }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 text-white font-medium rounded-lg transition-colors" style="background-color: #1B3C35;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Faire une demande
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-layouts.employee>
