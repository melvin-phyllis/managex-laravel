

<div x-data="downloadManager()" 
     x-on:start-download.window="startDownload($event.detail)"
     x-cloak>
    
    
    <div x-show="isDownloading"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm"
         style="z-index: 2147483647;">
        
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-sm w-full mx-4 text-center">
            
            <div x-show="status === 'downloading'" class="relative mx-auto w-20 h-20 mb-6">
                
                <svg class="w-20 h-20 animate-spin-slow" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="45" fill="none" stroke="#E5E7EB" stroke-width="8"/>
                    <circle cx="50" cy="50" r="45" fill="none" stroke="url(#downloadGradient)" stroke-width="8"
                        stroke-linecap="round" stroke-dasharray="283" :stroke-dashoffset="283 - (progress * 2.83)"
                        style="transition: stroke-dashoffset 0.3s ease;"/>
                    <defs>
                        <linearGradient id="downloadGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="#3B82F6"/>
                            <stop offset="100%" stop-color="#8B5CF6"/>
                        </linearGradient>
                    </defs>
                </svg>
                
                
                <div class="absolute inset-0 flex items-center justify-center">
                    <template x-if="fileType === 'pdf'">
                        <svg class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l5 5h-5V4z"/>
                        </svg>
                    </template>
                    <template x-if="fileType === 'excel'">
                        <svg class="w-8 h-8 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l5 5h-5V4z"/>
                        </svg>
                    </template>
                    <template x-if="fileType === 'csv'">
                        <svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l5 5h-5V4z"/>
                        </svg>
                    </template>
                    <template x-if="!fileType || !['pdf', 'excel', 'csv'].includes(fileType)">
                        <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </template>
                </div>
            </div>
            
            
            <div x-show="status === 'success'" class="relative mx-auto w-20 h-20 mb-6">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-green-500 animate-bounce-once" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
            
            
            <div x-show="status === 'error'" class="relative mx-auto w-20 h-20 mb-6">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
            </div>
            
            
            <h3 class="text-lg font-semibold mb-2" 
                :class="{
                    'text-gray-900': status === 'downloading',
                    'text-green-600': status === 'success',
                    'text-red-600': status === 'error'
                }">
                <span x-show="status === 'downloading'">Téléchargement en cours...</span>
                <span x-show="status === 'success'">Téléchargement effectué !</span>
                <span x-show="status === 'error'">Erreur de téléchargement</span>
            </h3>
            
            <p class="text-sm text-gray-500 mb-1" x-text="fileName"></p>
            
            <p class="text-xs text-gray-400" x-show="status === 'downloading'">
                Veuillez patienter, le fichier est en cours de génération...
            </p>
            <p class="text-xs text-green-600" x-show="status === 'success'">
                Le fichier a été téléchargé avec succès.
            </p>
            <p class="text-xs text-red-500" x-show="status === 'error'" x-text="errorMessage"></p>
            
            
            <div class="mt-6 h-2 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all duration-300 ease-out"
                     :class="{
                         'bg-gradient-to-r from-blue-500 to-purple-500': status === 'downloading',
                         'bg-green-500': status === 'success',
                         'bg-red-500': status === 'error'
                     }"
                     :style="'width: ' + progress + '%'">
                </div>
            </div>
            
            
            <p class="text-sm font-medium mt-2" 
               :class="{
                   'text-blue-600': status === 'downloading',
                   'text-green-600': status === 'success',
                   'text-red-600': status === 'error'
               }">
                <span x-text="progress + '%'"></span>
            </p>
            
            
            <button x-show="status === 'success' || status === 'error'"
                    @click="closeOverlay()"
                    class="mt-4 px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors">
                Fermer
            </button>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    
    @keyframes spin-slow {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    @keyframes bounce-once {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.2); }
    }
    
    .animate-spin-slow {
        animation: spin-slow 2s linear infinite;
    }
    
    .animate-bounce-once {
        animation: bounce-once 0.5s ease-out;
    }
</style>

<?php $__env->startPush('scripts'); ?>
<script nonce="<?php echo e($cspNonce ?? ''); ?>">
function downloadManager() {
    return {
        isDownloading: false,
        fileName: '',
        fileType: '',
        status: 'downloading', // 'downloading', 'success', 'error'
        progress: 0,
        errorMessage: '',
        
        async startDownload(detail) {
            const { url, filename, type } = detail;
            
            // Reset state
            this.isDownloading = true;
            this.fileName = filename || 'fichier';
            this.fileType = type || this.detectFileType(url);
            this.status = 'downloading';
            this.progress = 0;
            this.errorMessage = '';
            
            try {
                // Démarrer la progression simulée pour le début
                this.simulateProgress();
                
                // Télécharger le fichier via fetch
                const response = await fetch(url);
                
                if (!response.ok) {
                    throw new Error(`Erreur serveur: ${response.status}`);
                }
                
                // Récupérer la taille totale si disponible
                const contentLength = response.headers.get('content-length');
                const total = contentLength ? parseInt(contentLength, 10) : 0;
                
                // Lire le stream avec progression
                const reader = response.body.getReader();
                const chunks = [];
                let received = 0;
                
                while (true) {
                    const { done, value } = await reader.read();
                    
                    if (done) break;
                    
                    chunks.push(value);
                    received += value.length;
                    
                    // Mettre à jour la progression
                    if (total > 0) {
                        this.progress = Math.min(95, Math.round((received / total) * 100));
                    }
                }
                
                // Assembler le blob
                const blob = new Blob(chunks);
                
                // Progression à 100%
                this.progress = 100;
                this.status = 'success';
                
                // Déclencher le téléchargement
                const downloadUrl = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = downloadUrl;
                a.download = this.fileName;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(downloadUrl);
                
                // Fermer automatiquement après 1.5 secondes
                setTimeout(() => {
                    this.closeOverlay();
                }, 1500);
                
            } catch (error) {
                console.error('Erreur de téléchargement:', error);
                this.status = 'error';
                this.progress = 100;
                this.errorMessage = error.message || 'Une erreur est survenue lors du téléchargement.';
            }
        },
        
        simulateProgress() {
            // Progression simulée pendant que le serveur prépare le fichier
            const interval = setInterval(() => {
                if (this.status !== 'downloading' || this.progress >= 90) {
                    clearInterval(interval);
                    return;
                }
                // Progression lente et ralentissante
                const increment = Math.max(1, Math.floor((90 - this.progress) / 10));
                this.progress = Math.min(90, this.progress + increment);
            }, 200);
        },
        
        closeOverlay() {
            this.isDownloading = false;
            this.fileName = '';
            this.fileType = '';
            this.status = 'downloading';
            this.progress = 0;
            this.errorMessage = '';
        },
        
        detectFileType(url) {
            if (url.includes('pdf')) return 'pdf';
            if (url.includes('excel') || url.includes('xlsx')) return 'excel';
            if (url.includes('csv')) return 'csv';
            return '';
        }
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH D:\ManageX\resources\views/components/download-overlay.blade.php ENDPATH**/ ?>