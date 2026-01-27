<x-layouts.employee>
    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Header -->
        <a href="{{ route('employee.documents.index') }}" 
           class="inline-flex items-center text-gray-500 hover:text-gray-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Retour à mes documents
        </a>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Header -->
            <div class="p-6 bg-gradient-to-r from-green-500 to-emerald-600 text-white">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                        <span class="text-3xl">📤</span>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold">{{ $type->name }}</h1>
                        <p class="text-white/80">{{ $type->category->name }}</p>
                    </div>
                </div>
            </div>

            <!-- Info Box -->
            @if($type->description)
                <div class="p-4 bg-blue-50 border-b border-blue-100">
                    <p class="text-sm text-blue-700">ℹ️ {{ $type->description }}</p>
                </div>
            @endif

            @if($type->requires_validation)
                <div class="p-4 bg-yellow-50 border-b border-yellow-100">
                    <p class="text-sm text-yellow-700">🟡 Ce document nécessite une validation par les RH.</p>
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('employee.documents.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf
                <input type="hidden" name="document_type_id" value="{{ $type->id }}">

                <!-- File Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Fichier <span class="text-red-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-green-400 transition-colors cursor-pointer"
                         id="dropZone" onclick="document.getElementById('file').click()">
                        <input type="file" name="file" id="file" required class="hidden" 
                               accept=".{{ implode(',.', $type->allowed_extensions ?? ['pdf', 'jpg', 'jpeg', 'png']) }}">
                        <div id="uploadPlaceholder">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div>
                            <p class="text-gray-600">Cliquez ou glissez votre fichier ici</p>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ $type->getAllowedExtensionsString() }} • Max {{ $type->max_size_mb }}MB
                            </p>
                        </div>
                        <div id="filePreview" class="hidden">
                            <div class="flex items-center justify-center gap-3">
                                <span id="fileIcon" class="text-4xl">📄</span>
                                <div class="text-left">
                                    <p id="fileName" class="font-medium text-gray-900"></p>
                                    <p id="fileSize" class="text-sm text-gray-500"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @error('file')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Expiry Date (if type has expiry) -->
                @if($type->has_expiry_date)
                    <div>
                        <label for="expiry_date" class="block text-sm font-medium text-gray-700 mb-1">
                            Date d'expiration
                        </label>
                        <input type="date" name="expiry_date" id="expiry_date" value="{{ old('expiry_date') }}"
                               class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                        <p class="text-xs text-gray-500 mt-1">Si votre document a une date d'expiration, indiquez-la.</p>
                    </div>
                @endif

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Description (optionnel)
                    </label>
                    <textarea name="description" id="description" rows="2"
                              class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
                              placeholder="Notes ou informations supplémentaires...">{{ old('description') }}</textarea>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-4 pt-4 border-t">
                    <a href="{{ route('employee.documents.index') }}" 
                       class="px-6 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        📤 Envoyer
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        const fileInput = document.getElementById('file');
        const dropZone = document.getElementById('dropZone');
        const placeholder = document.getElementById('uploadPlaceholder');
        const preview = document.getElementById('filePreview');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const fileIcon = document.getElementById('fileIcon');

        fileInput.addEventListener('change', handleFile);

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('border-green-400', 'bg-green-50');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('border-green-400', 'bg-green-50');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-green-400', 'bg-green-50');
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                handleFile();
            }
        });

        function handleFile() {
            if (fileInput.files.length) {
                const file = fileInput.files[0];
                fileName.textContent = file.name;
                fileSize.textContent = formatBytes(file.size);
                
                const ext = file.name.split('.').pop().toLowerCase();
                fileIcon.textContent = ext === 'pdf' ? '📄' : '🖼️';
                
                placeholder.classList.add('hidden');
                preview.classList.remove('hidden');
            }
        }

        function formatBytes(bytes) {
            if (bytes >= 1048576) return (bytes / 1048576).toFixed(2) + ' MB';
            if (bytes >= 1024) return (bytes / 1024).toFixed(2) + ' KB';
            return bytes + ' B';
        }
    </script>
    @endpush
</x-layouts.employee>
