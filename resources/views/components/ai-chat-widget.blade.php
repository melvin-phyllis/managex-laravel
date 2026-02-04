@props(['chatRoute' => null, 'isAdmin' => false])

@php
    $resolvedRoute = $chatRoute ?? (auth()->user()?->role === 'admin' ? route('admin.ai.chat') : route('employee.ai.chat'));
    $isAdminUser = $isAdmin || auth()->user()?->role === 'admin';
@endphp

<div x-data="aiChatWidget()" class="fixed bottom-6 right-6 z-50" @keydown.escape.window="open = false">
    {{-- Chat Panel --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="absolute bottom-16 right-0 w-[350px] h-[500px] bg-white rounded-2xl shadow-2xl border border-gray-200 flex flex-col overflow-hidden">

        {{-- Header --}}
        <div class="px-4 py-3 flex items-center justify-between flex-shrink-0 {{ $isAdminUser ? 'bg-gradient-to-r from-violet-600 to-indigo-600' : 'bg-gradient-to-r from-emerald-600 to-teal-600' }}">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-white/20 backdrop-blur rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714a2.25 2.25 0 00.659 1.591L19 14.5M14.25 3.104c.251.023.501.05.75.082M19 14.5l-2.47 2.47a2.25 2.25 0 01-1.591.659H9.061a2.25 2.25 0 01-1.591-.659L5 14.5m14 0V17a2.25 2.25 0 01-2.25 2.25H7.25A2.25 2.25 0 015 17v-2.5"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-white">{{ $isAdminUser ? 'Assistant IA Admin' : 'Assistant RH' }}</h3>
                    <p class="text-[10px] text-white/70">Propulsé par Mistral AI</p>
                </div>
            </div>
            <button @click="open = false" class="w-7 h-7 flex items-center justify-center rounded-lg bg-white/10 hover:bg-white/20 text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Messages --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-3" x-ref="messagesContainer">
            {{-- Welcome message --}}
            <template x-if="messages.length === 0">
                <div class="text-center py-8">
                    <div class="w-14 h-14 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-900">Bonjour {{ auth()->user()->name }} !</p>
                    <p class="text-xs text-gray-500 mt-1">Comment puis-je vous aider ?</p>
                    <div class="mt-4 space-y-2">
                        @if($isAdminUser)
                            <button @click="sendQuickMessage('Quel est le taux de présence aujourd\'hui ?')" class="block w-full text-left text-xs bg-gray-50 hover:bg-violet-50 border border-gray-200 hover:border-violet-200 rounded-lg px-3 py-2 text-gray-600 hover:text-violet-700 transition-colors">
                                Quel est le taux de présence aujourd'hui ?
                            </button>
                            <button @click="sendQuickMessage('Quels départements ont le plus de retards ?')" class="block w-full text-left text-xs bg-gray-50 hover:bg-violet-50 border border-gray-200 hover:border-violet-200 rounded-lg px-3 py-2 text-gray-600 hover:text-violet-700 transition-colors">
                                Quels départements ont le plus de retards ?
                            </button>
                            <button @click="sendQuickMessage('Résume la situation RH de l\'entreprise')" class="block w-full text-left text-xs bg-gray-50 hover:bg-violet-50 border border-gray-200 hover:border-violet-200 rounded-lg px-3 py-2 text-gray-600 hover:text-violet-700 transition-colors">
                                Résume la situation RH de l'entreprise
                            </button>
                        @else
                            <button @click="sendQuickMessage('Combien de jours de congé me reste-t-il ?')" class="block w-full text-left text-xs bg-gray-50 hover:bg-emerald-50 border border-gray-200 hover:border-emerald-200 rounded-lg px-3 py-2 text-gray-600 hover:text-emerald-700 transition-colors">
                                Combien de jours de congé me reste-t-il ?
                            </button>
                            <button @click="sendQuickMessage('Quel est mon solde de retard ce mois ?')" class="block w-full text-left text-xs bg-gray-50 hover:bg-emerald-50 border border-gray-200 hover:border-emerald-200 rounded-lg px-3 py-2 text-gray-600 hover:text-emerald-700 transition-colors">
                                Quel est mon solde de retard ce mois ?
                            </button>
                            <button @click="sendQuickMessage('Comment faire une demande de congé ?')" class="block w-full text-left text-xs bg-gray-50 hover:bg-emerald-50 border border-gray-200 hover:border-emerald-200 rounded-lg px-3 py-2 text-gray-600 hover:text-emerald-700 transition-colors">
                                Comment faire une demande de congé ?
                            </button>
                        @endif
                    </div>
                </div>
            </template>

            {{-- Message bubbles --}}
            <template x-for="(msg, index) in messages" :key="index">
                <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                    <div :class="msg.role === 'user'
                        ? '{{ $isAdminUser ? "bg-gradient-to-br from-violet-500 to-indigo-600" : "bg-gradient-to-br from-emerald-500 to-teal-600" }} text-white rounded-2xl rounded-br-md'
                        : 'bg-gray-100 text-gray-800 rounded-2xl rounded-bl-md'"
                         class="max-w-[85%] px-3.5 py-2.5 text-sm leading-relaxed">
                        <div x-html="msg.role === 'assistant' ? formatMarkdown(msg.content) : escapeHtml(msg.content)"></div>
                    </div>
                </div>
            </template>

            {{-- Loading indicator --}}
            <div x-show="loading" class="flex justify-start">
                <div class="bg-gray-100 rounded-2xl rounded-bl-md px-4 py-3">
                    <div class="flex items-center gap-1.5">
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Input --}}
        <div class="border-t border-gray-200 p-3 flex-shrink-0">
            <form @submit.prevent="sendMessage" class="flex items-center gap-2">
                <input x-model="input"
                       x-ref="chatInput"
                       type="text"
                       placeholder="Posez votre question..."
                       maxlength="500"
                       :disabled="loading"
                       class="flex-1 text-sm border border-gray-300 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 {{ $isAdminUser ? 'focus:ring-violet-500 focus:border-violet-500' : 'focus:ring-emerald-500 focus:border-emerald-500' }} disabled:opacity-50 disabled:bg-gray-50"
                       @keydown.enter.prevent="sendMessage">
                <button type="submit"
                        :disabled="loading || !input.trim()"
                        class="w-10 h-10 text-white rounded-xl flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed transition-all flex-shrink-0 {{ $isAdminUser ? 'bg-gradient-to-r from-violet-500 to-indigo-600 hover:from-violet-600 hover:to-indigo-700' : 'bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    {{-- Floating Button --}}
    <button @click="toggleChat"
            class="w-14 h-14 text-white rounded-full shadow-lg hover:shadow-xl hover:scale-105 flex items-center justify-center transition-all duration-200 group relative {{ $isAdminUser ? 'bg-gradient-to-r from-violet-500 to-indigo-600' : 'bg-gradient-to-r from-emerald-500 to-teal-600' }}">
        {{-- Pulse animation when closed --}}
        <span x-show="!open" class="absolute inset-0 rounded-full {{ $isAdminUser ? 'bg-violet-400' : 'bg-emerald-400' }} animate-ping opacity-20"></span>
        {{-- Icon --}}
        <svg x-show="!open" class="w-6 h-6 relative" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
        <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
</div>

<script nonce="{{ $cspNonce ?? '' }}">
function aiChatWidget() {
    return {
        open: false,
        loading: false,
        input: '',
        messages: [],

        toggleChat() {
            this.open = !this.open;
            if (this.open) {
                this.$nextTick(() => this.$refs.chatInput?.focus());
            }
        },

        sendQuickMessage(text) {
            this.input = text;
            this.sendMessage();
        },

        async sendMessage() {
            const text = this.input.trim();
            if (!text || this.loading) return;

            this.messages.push({ role: 'user', content: text });
            this.input = '';
            this.loading = true;
            this.scrollToBottom();

            try {
                const history = this.messages.slice(0, -1).map(m => ({
                    role: m.role,
                    content: m.content
                }));

                const response = await fetch(@json($resolvedRoute), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ message: text, history })
                });

                if (response.status === 429) {
                    this.messages.push({
                        role: 'assistant',
                        content: 'Vous avez atteint la limite de messages. Veuillez patienter une minute avant de réessayer.'
                    });
                } else if (response.ok) {
                    const data = await response.json();
                    this.messages.push({ role: 'assistant', content: data.response });
                } else {
                    this.messages.push({
                        role: 'assistant',
                        content: 'Une erreur est survenue. Veuillez réessayer.'
                    });
                }
            } catch (error) {
                this.messages.push({
                    role: 'assistant',
                    content: 'Impossible de contacter le service. Vérifiez votre connexion.'
                });
            } finally {
                this.loading = false;
                this.scrollToBottom();
                this.$nextTick(() => this.$refs.chatInput?.focus());
            }
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.messagesContainer;
                if (container) container.scrollTop = container.scrollHeight;
            });
        },

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        },

        formatMarkdown(text) {
            if (!text) return '';
            let html = this.escapeHtml(text);
            // Bold
            html = html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
            // Line breaks
            html = html.replace(/\n/g, '<br>');
            // Bullet points
            html = html.replace(/^- (.+)/gm, '<span class="flex gap-1.5 items-start"><span class="{{ $isAdminUser ? 'text-violet-500' : 'text-emerald-500' }} mt-0.5">&#8226;</span><span>$1</span></span>');
            return html;
        }
    }
}
</script>
