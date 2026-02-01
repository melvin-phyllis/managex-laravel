<x-layouts.admin>
    <style>
        [x-cloak] { display: none !important; }

        /* Resizer styles */
        .resizer {
            width: 4px;
            background: transparent;
            cursor: col-resize;
            flex-shrink: 0;
            transition: background-color 0.2s;
            position: relative;
        }
        .resizer:hover,
        .resizer.resizing {
            background: #3b82f6;
        }
        .resizer::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 4px;
            height: 40px;
            background: #d1d5db;
            border-radius: 2px;
            opacity: 0;
            transition: opacity 0.2s;
        }
        .resizer:hover::before {
            opacity: 1;
            background: white;
        }

        /* Prevent text selection while resizing */
        .no-select {
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
        }
    </style>

    <div x-data="messagingApp()" x-init="init()">
        <div class="h-[calc(100vh-8rem)] flex bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
             :class="{ 'no-select': isResizing }">

        <!-- Sidebar - Liste des conversations -->
        <div class="border-r border-gray-200 flex flex-col bg-gray-50 flex-shrink-0 overflow-hidden"
             :class="{ 'hidden': selectedConversation && !isDesktop, 'flex': !selectedConversation || isDesktop }"
             :style="isDesktop ? 'width: ' + sidebarWidth + 'px' : 'width: 100%'"
             x-ref="sidebar">
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

            <!-- Conversations List -->
            <div class="flex-1 overflow-y-auto">
                <template x-for="conv in filteredConversations" :key="conv.id">
                    <div @click="selectConversation(conv)"
                         :class="selectedConversation?.id === conv.id ? 'bg-blue-50 border-l-4 border-blue-600' : 'hover:bg-gray-100'"
                         class="p-3 cursor-pointer transition-colors border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <!-- Avatar -->
                            <div class="relative flex-shrink-0">
                                <template x-if="conv.type === 'direct'">
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-semibold overflow-hidden"
                                         :class="getAvatarColor(conv.other_user?.name || conv.name)">
                                        <template x-if="conv.other_user?.avatar">
                                            <img :src="'/storage/' + conv.other_user.avatar" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!conv.other_user?.avatar">
                                            <span x-text="getInitials(conv.other_user?.name || conv.name)"></span>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="conv.type === 'group' || conv.type === 'channel'">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-500 to-blue-600 flex items-center justify-center text-white font-bold">
                                        <span x-text="conv.type === 'channel' ? '#' : getInitials(conv.name)"></span>
                                    </div>
                                </template>
                            </div>
                            <!-- Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start">
                                    <h3 class="font-medium text-gray-900 truncate"
                                        x-text="conv.type === 'direct' ? (conv.other_user?.name || conv.name) : conv.name"></h3>
                                    <span class="text-xs text-gray-400 flex-shrink-0 ml-2" x-text="formatTime(conv.last_message_at)"></span>
                                </div>
                                <p class="text-sm text-gray-500 truncate" x-text="conv.last_message || 'Aucun message'"></p>
                            </div>
                            <!-- Unread badge -->
                            <template x-if="conv.unread_count > 0">
                                <span class="bg-blue-600 text-white text-xs font-bold px-2 py-1 rounded-full flex-shrink-0" x-text="conv.unread_count"></span>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- Empty state -->
                <template x-if="filteredConversations.length === 0">
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <p>Aucune conversation</p>
                    </div>
                </template>
            </div>
        </div>

        <!-- Resizer Handle (desktop only) -->
        <div class="resizer hidden md:block"
             :class="{ 'resizing': isResizing }"
             @mousedown="startResize($event)"
             x-show="isDesktop"
             x-ref="resizer"></div>

        <!-- Main Chat Area -->
        <div class="flex-1 flex flex-col min-w-0"
             :class="{ 'hidden': !selectedConversation && !isDesktop, 'flex': selectedConversation || isDesktop }">
            <template x-if="selectedConversation">
                <div class="flex flex-col h-full">
                    <!-- Chat Header -->
                    <div class="h-16 border-b border-gray-200 flex items-center justify-between px-4 bg-white">
                        <div class="flex items-center gap-3">
                            <!-- Bouton retour (mobile uniquement) -->
                            <button @click="selectedConversation = null"
                                    class="md:hidden p-2 -ml-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <template x-if="selectedConversation.type === 'direct'">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold"
                                     :class="getAvatarColor(selectedConversation.other_user?.name)">
                                    <span x-text="getInitials(selectedConversation.other_user?.name)"></span>
                                </div>
                            </template>
                            <template x-if="selectedConversation.type !== 'direct'">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-blue-600 flex items-center justify-center text-white font-bold">
                                    <span x-text="selectedConversation.type === 'channel' ? '#' : getInitials(selectedConversation.name)"></span>
                                </div>
                            </template>
                            <div class="min-w-0">
                                <h3 class="font-semibold text-gray-900 truncate"
                                    x-text="selectedConversation.type === 'direct' ? selectedConversation.other_user?.name : selectedConversation.name"></h3>
                                <p class="text-xs text-gray-500" x-text="selectedConversation.type === 'channel' ? 'Canal' : (selectedConversation.type === 'group' ? selectedConversation.participants?.length + ' participants' : 'Message direct')"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Messages -->
                    <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50" id="messagesContainer" x-ref="messagesContainer">
                        <template x-for="message in messages" :key="message.id">
                            <div :class="(message.sender_id || message.user_id) == currentUserId ? 'flex justify-end' : 'flex justify-start'">
                                <div :class="(message.sender_id || message.user_id) == currentUserId ? 'bg-blue-600 text-white' : 'bg-white border border-gray-200 text-gray-900'"
                                     class="max-w-[85%] sm:max-w-md px-4 py-2 rounded-2xl shadow-sm">
                                    <template x-if="(message.sender_id || message.user_id) != currentUserId && selectedConversation.type !== 'direct'">
                                        <p class="text-xs font-semibold mb-1" :class="(message.sender_id || message.user_id) == currentUserId ? 'text-blue-100' : 'text-gray-600'" x-text="message.sender?.name || message.user?.name"></p>
                                    </template>
                                    <p class="text-sm break-words" x-text="message.content"></p>
                                    <p class="text-xs mt-1 opacity-70" x-text="formatMessageTime(message.created_at)"></p>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Input -->
                    <div class="p-3 sm:p-4 border-t border-gray-200 bg-white">
                        <form @submit.prevent="sendMessage()" class="flex gap-2 sm:gap-3">
                            <input type="text"
                                   x-model="newMessage"
                                   placeholder="Écrivez un message..."
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm sm:text-base">
                            <button type="submit"
                                    :disabled="!newMessage.trim()"
                                    class="p-2 sm:px-6 sm:py-2 bg-blue-600 text-white rounded-full hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </template>

            <!-- No conversation selected -->
            <template x-if="!selectedConversation">
                <div class="flex flex-1 items-center justify-center bg-gray-50">
                    <div class="text-center px-4">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-600">Sélectionnez une conversation</h3>
                        <p class="text-sm text-gray-400 mt-1">Choisissez une conversation ou créez-en une nouvelle</p>
                    </div>
                </div>
            </template>
        </div>
        </div>

        <!-- Modal Nouvelle Conversation -->
        <div x-show="showNewConversation" x-cloak
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
             @click.self="showNewConversation = false">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-4 sm:p-6 max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Nouvelle conversation</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select x-model="newConvType" class="w-full rounded-lg border-gray-300">
                            <option value="direct">Message direct</option>
                            <option value="group">Groupe</option>
                        </select>
                    </div>

                    <div x-show="newConvType === 'group'">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom du groupe</label>
                        <input type="text" x-model="newConvName" class="w-full rounded-lg border-gray-300" placeholder="Nom du groupe">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Participants</label>
                        <div class="max-h-48 overflow-y-auto border border-gray-200 rounded-lg">
                            <template x-for="user in availableUsers" :key="user.id">
                                <label class="flex items-center gap-3 p-2 hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" :value="user.id" x-model="selectedParticipants" class="rounded border-gray-300 text-blue-600">
                                    <span x-text="user.name" class="text-sm"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button @click="showNewConversation = false" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">Annuler</button>
                    <button @click="createConversation()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Créer</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function messagingApp() {
            return {
                conversations: @json($conversations ?? []),
                filteredConversations: [],
                selectedConversation: null,
                messages: [],
                newMessage: '',
                searchQuery: '',
                activeTab: 'all',
                showNewConversation: false,
                newConvType: 'direct',
                newConvName: '',
                selectedParticipants: [],
                availableUsers: @json($users ?? []),
                currentUserId: {{ auth()->id() }},
                pollingInterval: null,
                conversationPollingInterval: null,

                // Resizer
                isResizing: false,
                sidebarWidth: 320,
                minWidth: 250,
                maxWidth: 600,
                isDesktop: window.innerWidth >= 768,

                init() {
                    this.filterConversations();
                    this.$watch('activeTab', () => this.filterConversations());

                    // Load saved sidebar width
                    const savedWidth = localStorage.getItem('messaging_sidebar_width');
                    if (savedWidth) {
                        this.sidebarWidth = parseInt(savedWidth);
                    }

                    // Check if desktop
                    this.checkDesktop();
                    window.addEventListener('resize', () => this.checkDesktop());

                    // Add mouse event listeners for resizing
                    document.addEventListener('mousemove', (e) => this.handleResize(e));
                    document.addEventListener('mouseup', () => this.stopResize());

                    // Start polling for new messages
                    this.startPolling();

                    // Echo listener if available
                    if (typeof Echo !== 'undefined') {
                        Echo.private(`user.${this.currentUserId}`)
                            .listen('.new-message', (e) => {
                                if (this.selectedConversation?.id === e.conversation_id) {
                                    this.messages.push(e.message);
                                    this.$nextTick(() => this.scrollToBottom());
                                }
                                this.loadConversationsData();
                            });
                    }
                },

                checkDesktop() {
                    this.isDesktop = window.innerWidth >= 768;
                },

                startResize(e) {
                    if (!this.isDesktop) return;
                    this.isResizing = true;
                    e.preventDefault();
                },

                handleResize(e) {
                    if (!this.isResizing) return;

                    const container = this.$refs.sidebar.parentElement;
                    const containerRect = container.getBoundingClientRect();
                    let newWidth = e.clientX - containerRect.left;

                    // Apply constraints
                    newWidth = Math.max(this.minWidth, Math.min(this.maxWidth, newWidth));
                    this.sidebarWidth = newWidth;
                },

                stopResize() {
                    if (this.isResizing) {
                        this.isResizing = false;
                        // Save to localStorage
                        localStorage.setItem('messaging_sidebar_width', this.sidebarWidth.toString());
                    }
                },

                startPolling() {
                    // Poll for new messages every 3 seconds
                    this.pollingInterval = setInterval(() => {
                        if (this.selectedConversation) {
                            this.pollNewMessages();
                        }
                    }, 3000);

                    // Poll for conversation updates every 10 seconds
                    this.conversationPollingInterval = setInterval(() => {
                        this.loadConversationsData();
                    }, 10000);
                },

                async pollNewMessages() {
                    if (!this.selectedConversation || this.messages.length === 0) return;

                    try {
                        const lastId = this.messages[this.messages.length - 1].id;
                        const response = await fetch(`/admin/messaging/${this.selectedConversation.id}/messages?after=${lastId}`);
                        const data = await response.json();

                        if (data.data && data.data.length > 0) {
                            const existingIds = new Set(this.messages.map(m => m.id));
                            const newMessages = data.data.filter(m => !existingIds.has(m.id));

                            if (newMessages.length > 0) {
                                this.messages.push(...newMessages);
                                this.$nextTick(() => this.scrollToBottom());
                            }
                        }
                    } catch (error) {
                        console.error('Polling error:', error);
                    }
                },

                async loadConversationsData() {
                    try {
                        const response = await fetch('/messaging/api/conversations');
                        if (response.ok) {
                            const data = await response.json();
                            if (Array.isArray(data)) {
                                this.conversations = data.map(conv => {
                                    const existing = this.conversations.find(c => c.id === conv.id);
                                    return {
                                        ...conv,
                                        other_user: conv.other_user || existing?.other_user
                                    };
                                });
                                this.filterConversations();
                            }
                        }
                    } catch (error) {
                        console.error('Error loading conversations:', error);
                    }
                },

                filterConversations() {
                    let filtered = this.conversations;

                    if (this.activeTab === 'direct') {
                        filtered = filtered.filter(c => c.type === 'direct');
                    } else if (this.activeTab === 'group') {
                        filtered = filtered.filter(c => c.type === 'group' || c.type === 'channel');
                    }

                    if (this.searchQuery) {
                        const query = this.searchQuery.toLowerCase();
                        filtered = filtered.filter(c => {
                            const name = c.type === 'direct' ? c.other_user?.name : c.name;
                            return name?.toLowerCase().includes(query);
                        });
                    }

                    this.filteredConversations = filtered;
                },

                async selectConversation(conv) {
                    this.selectedConversation = conv;
                    await this.loadMessages(conv.id);
                    await this.markAsRead(conv.id);
                    // Reset unread count for this conversation
                    conv.unread_count = 0;
                },

                async markAsRead(conversationId) {
                    try {
                        await fetch(`/admin/messaging/${conversationId}/read`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                    } catch (error) {
                        console.error('Error marking as read:', error);
                    }
                },

                async loadMessages(conversationId) {
                    try {
                        const response = await fetch(`/admin/messaging/${conversationId}/messages`);
                        const data = await response.json();
                        this.messages = data.data;
                        this.$nextTick(() => this.scrollToBottom());
                    } catch (error) {
                        console.error('Error loading messages:', error);
                    }
                },

                async sendMessage() {
                    if (!this.newMessage.trim() || !this.selectedConversation) return;

                    try {
                        const response = await fetch(`/admin/messaging/${this.selectedConversation.id}/messages`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ content: this.newMessage })
                        });

                        const message = await response.json();
                        this.messages.push(message);
                        this.newMessage = '';
                        this.$nextTick(() => this.scrollToBottom());
                    } catch (error) {
                        console.error('Error sending message:', error);
                    }
                },

                async createConversation() {
                    try {
                        const response = await fetch('/admin/messaging', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                type: this.newConvType,
                                name: this.newConvName,
                                participants: this.selectedParticipants
                            })
                        });

                        const conv = await response.json();
                        this.conversations.unshift(conv);
                        this.filterConversations();
                        this.selectConversation(conv);
                        this.showNewConversation = false;
                        this.newConvName = '';
                        this.selectedParticipants = [];
                    } catch (error) {
                        console.error('Error creating conversation:', error);
                    }
                },

                scrollToBottom() {
                    const container = this.$refs.messagesContainer;
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                },

                getInitials(name) {
                    if (!name) return '?';
                    return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
                },

                getAvatarColor(name) {
                    const colors = ['bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-pink-500', 'bg-indigo-500', 'bg-yellow-500'];
                    const index = name ? name.charCodeAt(0) % colors.length : 0;
                    return colors[index];
                },

                formatTime(dateStr) {
                    if (!dateStr) return '';
                    const date = new Date(dateStr);
                    const now = new Date();
                    const diff = now - date;

                    if (diff < 86400000) {
                        return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
                    } else if (diff < 604800000) {
                        return date.toLocaleDateString('fr-FR', { weekday: 'short' });
                    } else {
                        return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' });
                    }
                },

                formatMessageTime(dateStr) {
                    const date = new Date(dateStr);
                    return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
                }
            }
        }
    </script>
</x-layouts.admin>
