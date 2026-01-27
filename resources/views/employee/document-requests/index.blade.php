<x-layouts.employee>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📋 Mes Demandes de Documents</h1>
                <p class="text-gray-500 mt-1">Demandez une attestation, un certificat ou tout autre document</p>
            </div>
            <a href="{{ route('employee.document-requests.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouvelle demande
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Liste des demandes -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Historique des demandes</h2>
            </div>

            @if($requests->count() > 0)
                <div class="divide-y divide-gray-100">
                    @foreach($requests as $request)
                        <div class="p-4 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center
                                        @if($request->status === 'approved') bg-green-100
                                        @elseif($request->status === 'rejected') bg-red-100
                                        @else bg-amber-100 @endif">
                                        @if($request->status === 'approved')
                                            <span class="text-xl">✅</span>
                                        @elseif($request->status === 'rejected')
                                            <span class="text-xl">❌</span>
                                        @else
                                            <span class="text-xl">⏳</span>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $request->type_label }}</p>
                                        <p class="text-sm text-gray-500">
                                            Demandé le {{ $request->created_at->format('d/m/Y à H:i') }}
                                        </p>
                                        @if($request->message)
                                            <p class="text-sm text-gray-600 mt-1">« {{ Str::limit($request->message, 80) }} »</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="px-3 py-1 text-sm rounded-full
                                        @if($request->status === 'approved') bg-green-100 text-green-700
                                        @elseif($request->status === 'rejected') bg-red-100 text-red-700
                                        @else bg-amber-100 text-amber-700 @endif">
                                        {{ $request->status_label }}
                                    </span>
                                    @if($request->hasDocument())
                                        <a href="{{ route('employee.document-requests.download', $request) }}" 
                                           class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                            Télécharger
                                        </a>
                                    @endif
                                </div>
                            </div>
                            @if($request->admin_response)
                                <div class="mt-3 ml-16 p-3 bg-gray-50 rounded-lg">
                                    <p class="text-sm text-gray-600">
                                        <span class="font-medium">Réponse RH :</span> {{ $request->admin_response }}
                                    </p>
                                    @if($request->responded_at)
                                        <p class="text-xs text-gray-400 mt-1">{{ $request->responded_at->format('d/m/Y à H:i') }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl">📄</span>
                    </div>
                    <p class="text-gray-500 mb-4">Vous n'avez pas encore fait de demande</p>
                    <a href="{{ route('employee.document-requests.create') }}" 
                       class="text-emerald-600 hover:text-emerald-700 font-medium">
                        Faire une demande →
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-layouts.employee>
