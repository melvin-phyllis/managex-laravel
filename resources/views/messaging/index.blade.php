<x-layouts.employee>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <div x-data="messagingApp()" x-init="init()">
        <div class="h-[calc(100vh-8rem)] flex bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        
        <!-- Sidebar - Liste des conversations -->
        <div class="w-80 border-r border-gray-200 flex flex-col bg-gray-50">
            <!-- Header -->
            <div class="p-4 border-b border-gray-200 bg-white">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-lg font-bold text-gray-900">Messages</h2>
                    <button @click="showNewConversation = true" 
                            class="p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </button>
                </div>
                <!-- Search -->
                <div class="relative">
                    <input type="text" 
                           x-model="searchQuery"
                           @input.debounce.300ms="filterConversations()"
                           placeholder="Rechercher..." 
                           class="w-full pl-10 pr-4 py-2 bg-gray-100 border-0 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Tabs -->
            <div class="flex border-b border-gray-200 bg-white">
                <button @click="activeTab = 'all'" 
                        :class="activeTab === 'all' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500'"
                        class="flex-1 py-2 text-sm font-medium border-b-2 hover:text-gray-700 transition-colors">
                    Tous
                </button>
                <button @click="activeTab = 'direct'" 
                        :class="activeTab === 'direct' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500'"
                        class="flex-1 py-2 text-sm font-medium border-b-2 hover:text-gray-700 transition-colors">
                    Directs
                </button>
                <button @click="activeTab = 'group'" 
                        :class="activeTab === 'group' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500'"
                        class="flex-1 py-2 text-sm font-medium border-b-2 hover:text-gray-700 transition-colors">
                    Groupes
                </button>
            </div>

            <!-- Conversation List -->
            <div class="flex-1 overflow-y-auto">
                <template x-if="loading">
                    <div class="flex items-center justify-center h-32">
                        <svg class="animate-spin h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </template>

                <template x-for="conv in filteredConversations" :key="conv.id">
                    <div @click="selectConversation(conv)"
                         :class="selectedConversation?.id === conv.id ? 'bg-blue-50 border-l-4 border-blue-600' : 'hover:bg-gray-100 border-l-4 border-transparent'"
                         class="p-4 cursor-pointer transition-colors">
                        <div class="flex items-start gap-3">
                            <!-- Avatar -->
                            <div class="relative flex-shrink-0">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-semibold">
                                    <span x-text="(conv.name || 'C').charAt(0).toUpperCase()"></span>
                                </div>
                                <template x-if="conv.is_pinned">
                                    <div class="absolute -top-1 -right-1 bg-yellow-400 rounded-full p-0.5">
                                        <svg class="w-3 h-3 text-yellow-800" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </div>
                                </template>
                            </div>
                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <h3 class="font-semibold text-gray-900 truncate" x-text="conv.name || 'Conversation'"></h3>
                                    <span class="text-xs text-gray-400" x-text="conv.last_message?.created_at || ''"></span>
                                </div>
                                <p class="text-sm text-gray-500 truncate mt-0.5" x-text="conv.last_message?.content || 'Aucun message'"></p>
                            </div>
                            <!-- Unread Badge -->
                            <template x-if="conv.unread_count > 0">
                                <span class="bg-blue-600 text-white text-xs font-bold px-2 py-0.5 rounded-full" x-text="conv.unread_count"></span>
                            </template>
                        </div>
                    </div>
                </template>

                <template x-if="!loading && filteredConversations.length === 0">
                    <div class="text-center py-12 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <p>Aucune conversation</p>
                    </div>
                </template>
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="flex-1 flex flex-col">
            <template x-if="!selectedConversation">
                <div class="flex-1 flex items-center justify-center bg-gray-50">
                    <div class="text-center">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-600">Sélectionnez une conversation</h3>
                        <p class="text-sm text-gray-400 mt-1">Choisissez une conversation ou créez-en une nouvelle</p>
                    </div>
                </div>
            </template>

            <template x-if="selectedConversation">
                <div class="flex-1 flex flex-col">
                    <!-- Chat Header -->
                    <div class="px-6 py-4 border-b border-gray-200 bg-white flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-semibold">
                                <span x-text="(selectedConversation?.name || 'C').charAt(0).toUpperCase()"></span>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900" x-text="selectedConversation?.name || 'Conversation'"></h3>
                                <p class="text-xs text-gray-500" x-text="selectedConversation.type === 'direct' ? 'Message direct' : (selectedConversation.participants?.length || 0) + ' participants'"></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button @click="togglePin()" class="p-2 text-gray-400 hover:text-yellow-500 hover:bg-gray-100 rounded-lg transition-colors">
                                <svg class="w-5 h-5" :class="selectedConversation.is_pinned ? 'text-yellow-500 fill-current' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                </svg>
                            </button>
                            <button @click="showConversationInfo = true" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Messages -->
                    <div class="flex-1 overflow-y-auto p-6 space-y-4 bg-gray-50" id="messagesContainer" @scroll="handleScroll">
                        <template x-if="loadingMessages">
                            <div class="flex items-center justify-center py-4">
                                <svg class="animate-spin h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </template>

                        <template x-for="(message, index) in messages" :key="message.id">
                            <div :class="message.sender?.id === currentUserId ? 'flex justify-end' : 'flex justify-start'">
                                <div :class="message.sender?.id === currentUserId ? 'bg-blue-600 text-white' : 'bg-white border border-gray-200'"
                                     class="max-w-md px-4 py-3 rounded-2xl shadow-sm">
                                    <!-- Sender name for others -->
                                    <template x-if="message.sender?.id !== currentUserId && message.sender">
                                        <p class="text-xs font-medium text-gray-500 mb-1" x-text="message.sender.name"></p>
                                    </template>
                                    
                                    <!-- Reply preview -->
                                    <template x-if="message.parent">
                                        <div class="mb-2 pl-2 border-l-2 border-gray-300 text-xs opacity-75">
                                            <span class="font-medium" x-text="message.parent.sender_name"></span>
                                            <p class="truncate" x-text="message.parent.content"></p>
                                        </div>
                                    </template>

                                    <!-- Content -->
                                    <div x-html="message.content_html || message.content"></div>

                                    <!-- Attachments -->
                                    <template x-if="message.attachments?.length > 0">
                                        <div class="mt-2 space-y-1">
                                            <template x-for="att in message.attachments" :key="att.id">
                                                <a :href="att.url" target="_blank" class="flex items-center gap-2 text-sm hover:underline">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                                    </svg>
                                                    <span x-text="att.name"></span>
                                                    <span class="opacity-75" x-text="'(' + att.size + ')'"></span>
                                                </a>
                                            </template>
                                        </div>
                                    </template>

                                    <!-- Footer -->
                                    <div class="flex items-center justify-between mt-1" :class="message.sender?.id === currentUserId ? 'text-blue-200' : 'text-gray-400'">
                                        <span class="text-xs" x-text="message.created_at_human"></span>
                                        <template x-if="message.is_edited">
                                            <span class="text-xs">(modifié)</span>
                                        </template>
                                    </div>

                                    <!-- Reactions -->
                                    <template x-if="Object.keys(message.reactions).length > 0">
                                        <div class="flex gap-1 mt-2">
                                            <template x-for="(count, emoji) in message.reactions" :key="emoji">
                                                <span class="bg-gray-100 px-2 py-0.5 rounded-full text-sm" x-text="emoji + ' ' + count"></span>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Message Input -->
                    <div class="p-4 border-t border-gray-200 bg-white">
                        <form @submit.prevent="sendMessage()" class="flex items-end gap-3">
                            <div class="flex-1">
                                <textarea x-model="newMessage" 
                                          @keydown.enter.meta="sendMessage()"
                                          @keydown.enter.ctrl="sendMessage()"
                                          placeholder="Écrivez votre message..."
                                          rows="1"
                                          class="w-full px-4 py-3 bg-gray-100 border-0 rounded-xl resize-none focus:ring-2 focus:ring-blue-500"
                                          x-ref="messageInput"></textarea>
                            </div>
                            <button type="submit" 
                                    :disabled="!newMessage.trim()"
                                    class="p-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- New Conversation Modal -->
    <div x-show="showNewConversation" 
         x-cloak
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
         @click.self="closeNewConversationModal()"
         @keydown.escape.window="closeNewConversationModal()">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Nouvelle conversation</h3>
            <form @submit.prevent="createConversation()">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select x-model="newConversationType" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="direct">Message direct</option>
                            <option value="group">Groupe</option>
                        </select>
                    </div>
                    <template x-if="newConversationType === 'group'">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nom du groupe</label>
                            <input type="text" x-model="newConversationName" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Nom du groupe...">
                        </div>
                    </template>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Participants</label>
                        <select x-model="selectedParticipants" multiple class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" size="5">
                            @foreach(\App\Models\User::where('id', '!=', auth()->id())->orderBy('name')->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Maintenez Ctrl pour sélectionner plusieurs</p>
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="button" @click="closeNewConversationModal()" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        Annuler
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Créer
                    </button>
                </div>
            </form>
        </div>
    </div>

    </div> <!-- Close x-data wrapper -->

    <script>
        function messagingApp() {
            return {
                conversations: [],
                filteredConversations: [],
                selectedConversation: null,
                messages: [],
                newMessage: '',
                searchQuery: '',
                activeTab: 'all',
                loading: true,
                loadingMessages: false,
                showNewConversation: false,
                showConversationInfo: false,
                newConversationType: 'direct',
                newConversationName: '',
                selectedParticipants: [],
                currentUserId: {{ auth()->id() }},
                pollingInterval: null,
                conversationPollingInterval: null,
                lastMessageId: 0,

                async init() {
                    await this.loadConversations();
                    this.$watch('activeTab', () => this.filterConversations());
                    
                    // Start polling for new messages
                    this.startPolling();
                    
                    // Cleanup on page unload
                    window.addEventListener('beforeunload', () => this.stopPolling());
                },

                startPolling() {
                    // Poll for new messages every 3 seconds when a conversation is selected
                    this.pollingInterval = setInterval(() => {
                        if (this.selectedConversation && !this.loadingMessages) {
                            this.pollNewMessages();
                        }
                    }, 3000);
                    
                    // Poll for conversation updates every 10 seconds
                    this.conversationPollingInterval = setInterval(() => {
                        this.pollConversations();
                    }, 10000);
                },

                stopPolling() {
                    if (this.pollingInterval) {
                        clearInterval(this.pollingInterval);
                        this.pollingInterval = null;
                    }
                    if (this.conversationPollingInterval) {
                        clearInterval(this.conversationPollingInterval);
                        this.conversationPollingInterval = null;
                    }
                },

                async pollNewMessages() {
                    if (!this.selectedConversation) return;
                    
                    try {
                        const lastId = this.messages.length > 0 ? this.messages[this.messages.length - 1].id : 0;
                        const response = await fetch(`/messaging/api/conversations/${this.selectedConversation.id}/messages?after=${lastId}`);
                        const data = await response.json();
                        
                        if (data.messages && data.messages.length > 0) {
                            // Filter out messages we already have
                            const existingIds = new Set(this.messages.map(m => m.id));
                            const newMessages = data.messages.filter(m => !existingIds.has(m.id));
                            
                            if (newMessages.length > 0) {
                                this.messages.push(...newMessages);
                                
                                // Scroll to bottom for new messages
                                this.$nextTick(() => {
                                    const container = document.getElementById('messagesContainer');
                                    if (container) container.scrollTop = container.scrollHeight;
                                });
                                
                                // Mark as read
                                await fetch(`/messaging/api/conversations/${this.selectedConversation.id}/read`, {
                                    method: 'POST',
                                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
                                });
                            }
                        }
                    } catch (error) {
                        console.error('Error polling messages:', error);
                    }
                },

                async pollConversations() {
                    try {
                        const response = await fetch('/messaging/api/conversations');
                        const newConversations = await response.json();
                        
                        // Update conversations while preserving selection
                        const selectedId = this.selectedConversation?.id;
                        this.conversations = newConversations;
                        this.filterConversations();
                        
                        // Update selected conversation data if still selected
                        if (selectedId) {
                            const updated = this.conversations.find(c => c.id === selectedId);
                            if (updated) {
                                this.selectedConversation = {...this.selectedConversation, ...updated};
                            }
                        }
                    } catch (error) {
                        console.error('Error polling conversations:', error);
                    }
                },

                async loadConversations() {
                    this.loading = true;
                    try {
                        const response = await fetch('/messaging/api/conversations');
                        this.conversations = await response.json();
                        this.filterConversations();
                    } catch (error) {
                        console.error('Error loading conversations:', error);
                    }
                    this.loading = false;
                },

                filterConversations() {
                    let filtered = [...this.conversations];
                    
                    if (this.activeTab !== 'all') {
                        filtered = filtered.filter(c => c.type === this.activeTab);
                    }
                    
                    if (this.searchQuery) {
                        const query = this.searchQuery.toLowerCase();
                        filtered = filtered.filter(c => c.name.toLowerCase().includes(query));
                    }
                    
                    this.filteredConversations = filtered;
                },

                async selectConversation(conv) {
                    this.selectedConversation = conv;
                    this.loadingMessages = true;
                    try {
                        const response = await fetch(`/messaging/api/conversations/${conv.id}/messages`);
                        const data = await response.json();
                        this.messages = data.messages;
                        
                        // Mark as read
                        await fetch(`/messaging/api/conversations/${conv.id}/read`, { method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'} });
                        conv.unread_count = 0;
                        
                        // Scroll to bottom
                        this.$nextTick(() => {
                            const container = document.getElementById('messagesContainer');
                            if (container) container.scrollTop = container.scrollHeight;
                        });
                    } catch (error) {
                        console.error('Error loading messages:', error);
                    }
                    this.loadingMessages = false;
                },

                async sendMessage() {
                    if (!this.newMessage.trim() || !this.selectedConversation) return;

                    try {
                        const response = await fetch(`/messaging/api/conversations/${this.selectedConversation.id}/messages`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ content: this.newMessage })
                        });
                        
                        const data = await response.json();
                        this.messages.push(data.message);
                        this.newMessage = '';
                        
                        // Scroll to bottom
                        this.$nextTick(() => {
                            const container = document.getElementById('messagesContainer');
                            if (container) container.scrollTop = container.scrollHeight;
                        });
                    } catch (error) {
                        console.error('Error sending message:', error);
                    }
                },

                async createConversation() {
                    if (this.selectedParticipants.length === 0) {
                        alert('Veuillez sélectionner au moins un participant.');
                        return;
                    }

                    try {
                        const response = await fetch('/messaging/api/conversations', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                type: this.newConversationType,
                                name: this.newConversationName,
                                participants: this.selectedParticipants
                            })
                        });
                        
                        if (!response.ok) {
                            throw new Error('Erreur lors de la création');
                        }
                        
                        const data = await response.json();
                        
                        // Close modal first
                        this.closeNewConversationModal();
                        
                        // Reload and select
                        await this.loadConversations();
                        if (data.conversation) {
                            this.selectConversation(data.conversation);
                        }
                    } catch (error) {
                        console.error('Error creating conversation:', error);
                        alert('Erreur lors de la création de la conversation.');
                    }
                },

                closeNewConversationModal() {
                    this.showNewConversation = false;
                    this.newConversationType = 'direct';
                    this.newConversationName = '';
                    this.selectedParticipants = [];
                },

                async togglePin() {
                    if (!this.selectedConversation) return;
                    try {
                        const response = await fetch(`/messaging/api/conversations/${this.selectedConversation.id}/pin`, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        });
                        const data = await response.json();
                        this.selectedConversation.is_pinned = data.is_pinned;
                        await this.loadConversations();
                    } catch (error) {
                        console.error('Error toggling pin:', error);
                    }
                },

                handleScroll(e) {
                    // Load more messages when scrolling to top
                    if (e.target.scrollTop === 0 && this.messages.length > 0) {
                        // TODO: Implement infinite scroll for older messages
                    }
                }
            };
        }
    </script>
</x-layouts.employee>
