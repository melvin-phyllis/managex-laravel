<x-layouts.admin>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.documents.index') }}" 
                   class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">ðŸŸ¡ Documents en Attente</h1>
                    <p class="text-gray-500">{{ $documents->total() }} documents nécessitant une validation</p>
                </div>
            </div>
        </div>

        <!-- Bulk Actions -->
        @if($documents->count() > 0)
        <form id="bulkForm" action="{{ route('admin.documents.bulk-validate') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            @csrf
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="selectAll" class="rounded text-green-600 focus:ring-green-500">
                    <span class="text-sm text-gray-600">Tout sélectionner</span>
                </label>
                <div class="flex gap-2">
                    <button type="submit" name="action" value="approve" 
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50"
                            id="approveBtn" disabled>
                        âœ… Approuver la sélection
                    </button>
                </div>
            </div>
        @endif

        <!-- Documents Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($documents as $document)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                    <div class="p-5">
                        <div class="flex items-start gap-4">
                            <label class="flex-shrink-0 cursor-pointer">
                                <input type="checkbox" name="document_ids[]" value="{{ $document->id }}"
                                       class="doc-checkbox rounded text-green-600 focus:ring-green-500">
                            </label>
                            <div class="flex-shrink-0 w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                                <span class="text-2xl">{{ $document->file_icon }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-900 truncate">{{ $document->title }}</h3>
                                <p class="text-sm text-gray-500">{{ $document->type->name }}</p>
                            </div>
                        </div>

                        <div class="mt-4 space-y-2 text-sm">
                            <div class="flex items-center gap-2">
                                <span class="text-gray-500">Employé:</span>
                                <a href="{{ route('admin.employees.show', $document->user) }}" 
                                   class="text-green-600 hover:underline">{{ $document->user->name }}</a>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-gray-500">Fichier:</span>
                                <span class="text-gray-900">{{ $document->original_filename }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-gray-500">Taille:</span>
                                <span class="text-gray-900">{{ $document->file_size_formatted }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-gray-500">Envoyé:</span>
                                <span class="text-gray-900">{{ $document->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 p-4 bg-gray-50 flex items-center justify-between gap-2">
                        <a href="{{ route('admin.documents.download', $document) }}" 
                           class="flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Voir
                        </a>
                        <div class="flex gap-2">
                            <button onclick="validateDoc({{ $document->id }}, 'approve')" 
                                    class="px-3 py-1 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">
                                âœ… Approuver
                            </button>
                            <button onclick="showReject({{ $document->id }})" 
                                    class="px-3 py-1 bg-red-100 text-red-700 text-sm rounded-lg hover:bg-red-200">
                                âŒ Rejeter
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-3xl">âœ…</span>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Aucun document en attente</h3>
                    <p class="text-gray-500 mt-1">Tous les documents ont été traités ! ðŸŽ</p>
                </div>
            @endforelse
        </div>

        @if($documents->count() > 0)
        </form>
        @endif

        <!-- Pagination -->
        @if($documents->hasPages())
            <div class="flex justify-center">
                {{ $documents->links() }}
            </div>
        @endif
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">âŒ Rejeter le document</h3>
            <form id="rejectForm" method="POST">
                @csrf
                <input type="hidden" name="action" value="reject">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Motif du rejet *</label>
                    <textarea name="rejection_reason" rows="3" required
                              class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500"
                              placeholder="Ex: Image floue, document illisible..."></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeReject()" 
                            class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Rejeter
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script nonce="{{ $cspNonce ?? '' }}">
        // Bulk selection
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.doc-checkbox');
        const approveBtn = document.getElementById('approveBtn');

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateButton();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateButton);
        });

        function updateButton() {
            const checked = document.querySelectorAll('.doc-checkbox:checked').length;
            if (approveBtn) {
                approveBtn.disabled = checked === 0;
                approveBtn.textContent = checked > 0 
                    ? `âœ… Approuver la sélection (${checked})` 
                    : 'âœ… Approuver la sélection';
            }
        }

        // Single validation
        function validateDoc(id, action) {
            fetch(`/admin/documents/${id}/validate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ action: action })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) location.reload();
            });
        }

        function showReject(id) {
            document.getElementById('rejectForm').action = `/admin/documents/${id}/validate`;
            document.getElementById('rejectModal').classList.remove('hidden');
            document.getElementById('rejectModal').classList.add('flex');
        }

        function closeReject() {
            document.getElementById('rejectModal').classList.add('hidden');
            document.getElementById('rejectModal').classList.remove('flex');
        }
    </script>
    @endpush
</x-layouts.admin>
