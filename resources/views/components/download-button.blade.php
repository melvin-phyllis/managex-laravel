@props([
    'url',
    'filename' => 'fichier',
    'type' => 'default', // default, pdf, excel, csv
    'size' => 'md', // sm, md, lg
    'icon' => true
])

@php
    $typeStyles = [
        'default' => 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50',
        'pdf' => 'bg-red-50 text-red-700 border-red-200 hover:bg-red-100',
        'excel' => 'bg-green-50 text-green-700 border-green-200 hover:bg-green-100',
        'csv' => 'bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-100',
    ];
    
    $sizeStyles = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2.5 text-sm',
        'lg' => 'px-5 py-3 text-base',
    ];
    
    $iconSizes = [
        'sm' => 'w-3.5 h-3.5',
        'md' => 'w-4 h-4',
        'lg' => 'w-5 h-5',
    ];
@endphp

<button 
    type="button"
    x-data="downloadButton('{{ $url }}', '{{ $filename }}')"
    @click="startDownload()"
    :disabled="downloading"
    {{ $attributes->merge([
        'class' => 'inline-flex items-center font-medium rounded-xl border transition-all shadow-sm gap-2 ' . 
                   $typeStyles[$type] . ' ' . $sizeStyles[$size] . 
                   ' disabled:opacity-50 disabled:cursor-wait'
    ]) }}
>
    {{-- Icône ou Spinner --}}
    <template x-if="!downloading">
        @if($icon)
            @if($type === 'pdf')
                <svg class="{{ $iconSizes[$size] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            @elseif($type === 'excel')
                <svg class="{{ $iconSizes[$size] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            @elseif($type === 'csv')
                <svg class="{{ $iconSizes[$size] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
            @else
                <svg class="{{ $iconSizes[$size] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
            @endif
        @endif
    </template>
    
    {{-- Spinner pendant le téléchargement --}}
    <template x-if="downloading">
        <svg class="{{ $iconSizes[$size] }} animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </template>
    
    {{-- Texte --}}
    <span x-text="downloading ? 'Préparation...' : '{{ $slot }}'"></span>
</button>

@once
@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
function downloadButton(url, filename) {
    return {
        downloading: false,
        
        async startDownload() {
            if (this.downloading) return;
            
            this.downloading = true;
            
            try {
                // Créer un iframe caché pour le téléchargement
                const iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                iframe.src = url;
                document.body.appendChild(iframe);
                
                // Vérifier périodiquement si le téléchargement a commencé
                // En surveillant les cookies ou après un délai raisonnable
                setTimeout(() => {
                    this.downloading = false;
                    document.body.removeChild(iframe);
                    
                    // Afficher une notification de succès
                    if (typeof Toastify !== 'undefined') {
                        Toastify({
                            text: `Téléchargement de "${filename}" terminé`,
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#10B981",
                            stopOnFocus: true,
                        }).showToast();
                    }
                }, 3000); // Délai estimé pour la préparation
                
            } catch (error) {
                console.error('Erreur de téléchargement:', error);
                this.downloading = false;
                
                if (typeof Toastify !== 'undefined') {
                    Toastify({
                        text: "Erreur lors du téléchargement",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#EF4444",
                    }).showToast();
                }
            }
        }
    }
}
</script>
@endpush
@endonce
