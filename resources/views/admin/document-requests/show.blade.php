<x-layouts.admin>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center gap-4 animate-fade-in-up">
            <a href="{{ route('admin.document-requests.index') }}" 
               class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📋 Demande de {{ $documentRequest->type_label }}</h1>
                <p class="text-gray-500">Par {{ $documentRequest->user->name }}</p>
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in-up animation-delay-100">
            <!-- Détails de la demande -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Détails de la demande</h2>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Type de document</p>
                            <p class="font-medium text-gray-900">{{ $documentRequest->type_label }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Date de demande</p>
                            <p class="font-medium text-gray-900">{{ $documentRequest->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Statut</p>
                            <span class="inline-flex px-2 py-1 text-xs rounded-full
                                @if($documentRequest->status === 'approved') bg-green-100 text-green-700
                                @elseif($documentRequest->status === 'rejected') bg-red-100 text-red-700
                                @else bg-amber-100 text-amber-700 @endif">
                                {{ $documentRequest->status_label }}
                            </span>
                        </div>
                    </div>

                    @if($documentRequest->message)
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-500 mb-1">Message de l'employé</p>
                            <p class="text-gray-900">{{ $documentRequest->message }}</p>
                        </div>
                    @endif
                </div>

                <!-- Formulaire de réponse (si en attente) -->
                @if($documentRequest->isPending())
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">✅ Approuver avec document</h2>
                        
                        <form action="{{ route('admin.document-requests.respond', $documentRequest) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Message de réponse *</label>
                                <textarea name="admin_response" rows="3" required
                                          placeholder="Ex: Voici votre attestation de travail..."
                                          class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">{{ old('admin_response') }}</textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Document à joindre *</label>
                                <input type="file" name="document" required accept=".pdf,.doc,.docx"
                                       class="w-full rounded-lg border border-gray-300 p-2 focus:border-emerald-500 focus:ring-emerald-500">
                                <p class="text-sm text-gray-500 mt-1">PDF, DOC ou DOCX - Max 10 Mo</p>
                            </div>

                            <button type="submit" class="w-full px-4 py-2 bg-emerald-600 text-white font-medium rounded-lg hover:bg-emerald-700">
                                ✅ Approuver et envoyer le document
                            </button>
                        </form>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">❌ Refuser la demande</h2>
                        
                        <form action="{{ route('admin.document-requests.reject', $documentRequest) }}" method="POST" class="space-y-4">
                            @csrf
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Motif du refus *</label>
                                <textarea name="admin_response" rows="3" required
                                          placeholder="Ex: Nous ne pouvons pas fournir ce document car..."
                                          class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500"></textarea>
                            </div>

                            <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700"
                                    onclick="return confirm('Êtes-vous sûr de vouloir refuser cette demande ?')">
                                ❌ Refuser la demande
                            </button>
                        </form>
                    </div>
                @else
                    <!-- Réponse déjà donnée -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Réponse</h2>
                        
                        <div class="p-4 rounded-lg @if($documentRequest->isApproved()) bg-green-50 @else bg-red-50 @endif">
                            <p class="text-sm text-gray-500 mb-1">Par {{ $documentRequest->admin->name ?? 'Admin' }}</p>
                            <p class="@if($documentRequest->isApproved()) text-green-800 @else text-red-800 @endif">{{ $documentRequest->admin_response }}</p>
                            @if($documentRequest->responded_at)
                                <p class="text-xs text-gray-400 mt-2">{{ $documentRequest->responded_at->format('d/m/Y à H:i') }}</p>
                            @endif
                        </div>

                        @if($documentRequest->hasDocument())
                            <div class="mt-4 p-4 bg-blue-50 rounded-lg flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl">📄</span>
                                    <span class="text-blue-800">{{ $documentRequest->document_name }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Sidebar - Info employé -->
            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">👤 Employé</h3>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-emerald-400 to-green-500 rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-3">
                            {{ strtoupper(substr($documentRequest->user->name, 0, 1)) }}
                        </div>
                        <p class="font-medium text-gray-900">{{ $documentRequest->user->name }}</p>
                        <p class="text-sm text-gray-500">{{ $documentRequest->user->position->name ?? 'Non défini' }}</p>
                        <p class="text-sm text-gray-500">{{ $documentRequest->user->email }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
