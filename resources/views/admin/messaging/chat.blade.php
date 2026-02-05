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
        .chat-messages-area {
            background-color: #e5ddd5;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23d4cdc4' fill-opacity='0.4'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .voice-wave-bar { min-height: 8px; }
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
                            class="p-2 text-white rounded-lg transition-colors" style="background: linear-gradient(135deg, #5680E9, #5AB9EA);">
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
                           class="w-full pl-10 pr-4 py-2 bg-gray-100 border-0 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Tabs -->
            <div class="flex border-b border-gray-200 bg-white">
                <button @click="activeTab = 'all'"
                        :class="activeTab === 'all' ? 'border-b-2 text-white' : 'border-transparent text-gray-500'"
                        :style="activeTab === 'all' ? 'border-color: #5680E9; background: linear-gradient(135deg, #5680E9, #84CEEB);' : ''"
                        class="flex-1 py-2 text-sm font-medium hover:text-gray-700 transition-colors">
                    Tous
                </button>
                <button @click="activeTab = 'direct'"
                        :class="activeTab === 'direct' ? 'border-b-2 text-white' : 'border-transparent text-gray-500'"
                        :style="activeTab === 'direct' ? 'border-color: #5680E9; background: linear-gradient(135deg, #5680E9, #84CEEB);' : ''"
                        class="flex-1 py-2 text-sm font-medium hover:text-gray-700 transition-colors">
                    Directs
                </button>
                <button @click="activeTab = 'group'"
                        :class="activeTab === 'group' ? 'border-b-2 text-white' : 'border-transparent text-gray-500'"
                        :style="activeTab === 'group' ? 'border-color: #5680E9; background: linear-gradient(135deg, #5680E9, #84CEEB);' : ''"
                        class="flex-1 py-2 text-sm font-medium hover:text-gray-700 transition-colors">
                    Groupes
                </button>
            </div>

            <!-- Conversations List -->
            <div class="flex-1 overflow-y-auto">
                <template x-for="conv in filteredConversations" :key="conv.id">
                    <div @click="selectConversation(conv)"
                         :class="selectedConversation?.id === conv.id ? 'border-l-4' : 'hover:bg-gray-100'"
                         :style="selectedConversation?.id === conv.id ? 'background: linear-gradient(90deg, #5680E920, transparent); border-color: #5680E9;' : ''"
                         class="p-3 cursor-pointer transition-colors border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <!-- Avatar -->
                            <div class="relative flex-shrink-0">
                                <template x-if="conv.type === 'direct'">
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-semibold overflow-hidden"
                                         :class="getAvatarColor(conv.other_user?.name || conv.name)">
                                        <template x-if="conv.other_user?.avatar">
                                            <img :src="conv.other_user.avatar" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!conv.other_user?.avatar">
                                            <span x-text="getInitials(conv.other_user?.name || conv.name)"></span>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="conv.type === 'group' || conv.type === 'channel'">
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold" style="background: linear-gradient(135deg, #8860D0, #5680E9);">
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
                                <span class="text-white text-xs font-bold px-2 py-1 rounded-full flex-shrink-0" style="background: linear-gradient(135deg, #5680E9, #5AB9EA);" x-text="conv.unread_count"></span>
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
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold" style="background: linear-gradient(135deg, #8860D0, #5680E9);">
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
                    <div class="flex-1 overflow-y-auto p-4 space-y-3 chat-messages-area" id="messagesContainer" x-ref="messagesContainer">
                        <template x-for="message in messages" :key="message.id">
                            <div :class="(message.sender_id || message.user_id) == currentUserId ? 'flex justify-end' : 'flex justify-start'">
                                <div :class="[
                                    (message.sender_id || message.user_id) == currentUserId ? 'bg-[#dcf8c6] text-gray-900 rounded-tl-xl rounded-tr-xl rounded-bl-xl rounded-br-md' : 'bg-white text-gray-900 rounded-tl-xl rounded-tr-xl rounded-br-xl rounded-bl-md',
                                    'max-w-[85%] sm:max-w-md px-3 py-2 shadow-md'
                                ]">
                                    <template x-if="(message.sender_id || message.user_id) != currentUserId && selectedConversation.type !== 'direct'">
                                        <p class="text-xs font-semibold text-emerald-700 mb-0.5" x-text="message.sender?.name || message.user?.name"></p>
                                    </template>
                                    <template x-if="message.attachments && message.attachments.length">
                                        <div class="space-y-2 mt-1">
                                            <template x-for="att in message.attachments" :key="att.id">
                                                <div>
                                                    <template x-if="att.is_image">
                                                        <a :href="att.url" :data-lightbox="'chat-' + message.id" :data-title="att.name" class="block rounded-lg overflow-hidden border border-gray-200/50 max-w-xs hover:opacity-95 transition-opacity">
                                                            <img :src="att.url" :alt="att.name" class="max-h-56 w-full object-cover"/>
                                                        </a>
                                                    </template>
                                                    <template x-if="isAudioAtt(att)">
                                                        <div class="flex items-center gap-2 py-1 px-2 rounded-xl min-w-[200px] max-w-[260px]"
                                                             :class="(message.sender_id || message.user_id) == currentUserId ? 'bg-white/60' : 'bg-gray-100'">
                                                            <button type="button" @click="playPauseVoice(att)" class="flex-shrink-0 w-9 h-9 rounded-full flex items-center justify-center transition bg-emerald-500/80 text-white hover:bg-emerald-600">
                                                                <template x-if="voicePlayingId === att.id">
                                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>
                                                                </template>
                                                                <template x-if="voicePlayingId !== att.id">
                                                                    <svg class="w-5 h-5 ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                                                </template>
                                                            </button>
                                                            <div class="flex-1 flex items-center justify-center gap-0.5 h-7">
                                                                <template x-for="(h, i) in voiceWaveBars" :key="i">
                                                                    <div class="voice-wave-bar w-1 rounded-full flex-shrink-0 transition-colors duration-150"
                                                                         :style="'height: ' + (h * 100) + '%'"
                                                                         :class="(voicePlayingId === att.id && voiceDuration > 0 && (i / voiceWaveBars.length) < (voiceCurrentTime / voiceDuration)) ? 'bg-emerald-600' : 'bg-gray-400/60'"></div>
                                                                </template>
                                                            </div>
                                                            <span class="text-xs font-medium w-9 text-right flex-shrink-0" x-text="voicePlayingId === att.id ? formatVoiceDuration(voiceCurrentTime) : formatVoiceDuration(voiceDurations[att.id] || 0)"></span>
                                                            <div class="w-7 h-7 rounded-full bg-gray-400/40 flex items-center justify-center flex-shrink-0">
                                                                <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3zm5.91-3c-.49 0-.9.36-.98.85C16.52 14.2 14.47 16 12 16s-4.52-1.8-4.93-4.15c-.08-.49-.49-.85-.98-.85-.61 0-1.09.54-1 1.14.49 3 2.89 5.35 5.92 5.86V20H6v-2h1v-2c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v2H2v2H0v-4c0-.55.45-1 1-1h1.08C2.58 12.94 2 11.54 2 10c0-1.1.9-2 2-2 .55 0 1 .45 1 1s-.45 1-1 1c-.28 0-.53.11-.71.29-.18.18-.29.43-.29.71 0 .53.29.99.73 1.26 1.4-.63 2.99-1 4.27-1 .55 0 1 .45 1 1s-.45 1-1 1c-.55 0-1 .45-1 1v1z"/></svg>
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <template x-if="!att.is_image && !isAudioAtt(att)">
                                                        <a :href="att.download_url" class="inline-flex items-center gap-2 text-sm underline" x-text="att.name"></a>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    <p class="text-sm break-words mt-1" x-show="message.content" x-text="message.content"></p>
                                    <div class="flex items-center justify-end gap-1 mt-0.5" :class="(message.sender_id || message.user_id) == currentUserId ? 'text-emerald-700/80' : 'text-gray-500'">
                                        <span class="text-[11px]" x-text="formatMessageTime(message.created_at)"></span>
                                        <template x-if="(message.sender_id || message.user_id) == currentUserId">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.7 7.3l-6.9 6.9-2.8-2.8-1.4 1.4 4.2 4.2 8.3-8.3zM12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2z"/></svg>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Input -->
                    <div class="p-3 sm:p-4 border-t border-gray-200 bg-white">
                        <div x-show="pendingImages.length > 0" class="flex gap-2 mb-3 overflow-x-auto pb-2">
                            <template x-for="(file, idx) in pendingImages" :key="idx">
                                <div class="relative flex-shrink-0">
                                    <img :src="file.preview" class="w-16 h-16 object-cover rounded-lg border border-gray-200"/>
                                    <button type="button" @click="removePendingImage(idx)" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600">éƒâ€”</button>
                                </div>
                            </template>
                        </div>
                        <form @submit.prevent="sendMessageOrAttachments()" class="flex items-center gap-2 sm:gap-3">
                            <input type="file" x-ref="imageInput" @change="onImageSelected($event)" accept="image/*" multiple class="hidden"/>
                            <button type="button" @click="$refs.imageInput.click()" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-full transition-colors flex-shrink-0" title="Envoyer une image">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </button>
                            <button type="button" @click="toggleVoiceRecord()" :class="isRecording ? 'bg-red-500 hover:bg-red-600 text-white' : 'text-gray-500 hover:text-blue-600 hover:bg-blue-50'" class="p-2 rounded-full transition-colors flex-shrink-0" :title="isRecording ? 'Arréªter et envoyer' : 'Message vocal'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v3m0 0V6a7 7 0 0114 0v3m-7 4a7 7 0 009.5 1.5"/>
                                </svg>
                            </button>
                            <input type="text"
                                   x-model="newMessage"
                                   placeholder="éƒâ€°crivez un message..."
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-full focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm sm:text-base">
                            <button type="submit"
                                    :disabled="!canSend()"
                                    class="p-2 sm:px-6 sm:py-2 text-white rounded-full disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex-shrink-0" style="background: linear-gradient(135deg, #5680E9, #5AB9EA);">
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
                                    <input type="checkbox" :value="user.id" x-model="selectedParticipants" class="rounded border-gray-300 text-indigo-600">
                                    <span x-text="user.name" class="text-sm"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button @click="showNewConversation = false" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">Annuler</button>
                    <button @click="createConversation()" class="px-4 py-2 text-white rounded-lg" style="background: linear-gradient(135deg, #5680E9, #5AB9EA);">Créer</button>
                </div>
            </div>
        </div>
    </div>

    <script nonce="{{ $cspNonce ?? '' }}">
        function messagingApp() {
            return {
                conversations: @json($conversations ?? []),
                filteredConversations: [],
                selectedConversation: null,
                messages: [],
                newMessage: '',
                pendingImages: [],
                isRecording: false,
                mediaRecorder: null,
                audioChunks: [],
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

                voiceAudio: null,
                voicePlayingId: null,
                voiceCurrentTime: 0,
                voiceDuration: 0,
                voiceDurations: {},
                voiceWaveBars: [0.5,0.85,0.45,0.7,0.6,0.9,0.4,0.75,0.55,0.8,0.5,0.65,0.7,0.45,0.9,0.6,0.5,0.85,0.4,0.75,0.6,0.7,0.5,0.8],

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

                    this.voiceAudio = new Audio();
                    this.voiceAudio.addEventListener('timeupdate', () => { this.voiceCurrentTime = this.voiceAudio.currentTime; });
                    this.voiceAudio.addEventListener('loadedmetadata', () => {
                        this.voiceDuration = this.voiceAudio.duration;
                        if (this.voicePlayingId) this.voiceDurations[this.voicePlayingId] = this.voiceAudio.duration;
                    });
                    this.voiceAudio.addEventListener('ended', () => { this.voicePlayingId = null; this.voiceCurrentTime = 0; });
                    this.voiceAudio.addEventListener('pause', () => {
                        if (this.voiceAudio.ended || this.voiceAudio.currentTime >= this.voiceAudio.duration) return;
                        this.voicePlayingId = null;
                    });

                    // Echo listener if available (Echo might be defined but null if Pusher not configured)
                    if (typeof Echo !== 'undefined' && Echo !== null) {
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

                fetchWithTimeout(url, options = {}, timeoutMs = 15000) {
                    const controller = new AbortController();
                    const id = setTimeout(() => controller.abort(), timeoutMs);
                    return fetch(url, { ...options, signal: controller.signal }).finally(() => clearTimeout(id));
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
                        const response = await this.fetchWithTimeout(`/admin/messaging/${this.selectedConversation.id}/messages?after=${lastId}`);
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
                        const response = await this.fetchWithTimeout('/messaging/api/conversations');
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

                canSend() {
                    return this.selectedConversation && (this.newMessage.trim() || this.pendingImages.length > 0);
                },

                async sendMessageOrAttachments() {
                    if (!this.selectedConversation) return;
                    if (this.pendingImages.length > 0) {
                        await this.uploadImages();
                        return;
                    }
                    if (this.newMessage.trim()) {
                        await this.sendMessage();
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

                onImageSelected(event) {
                    const files = event.target.files;
                    if (!files || !files.length) return;
                    for (let i = 0; i < files.length; i++) {
                        const file = files[i];
                        if (!file.type.startsWith('image/')) continue;
                        this.pendingImages.push({ file, preview: URL.createObjectURL(file) });
                    }
                    event.target.value = '';
                },

                removePendingImage(index) {
                    URL.revokeObjectURL(this.pendingImages[index].preview);
                    this.pendingImages.splice(index, 1);
                },

                async uploadImages() {
                    if (!this.selectedConversation || this.pendingImages.length === 0) return;
                    const formData = new FormData();
                    this.pendingImages.forEach(({ file }) => formData.append('files[]', file));
                    if (this.newMessage.trim()) formData.append('content', this.newMessage);
                    const csrf = document.querySelector('meta[name="csrf-token"]').content;
                    try {
                        const response = await fetch(`/messaging/api/conversations/${this.selectedConversation.id}/attachments`, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': csrf },
                            body: formData
                        });
                        if (!response.ok) {
                            const err = await response.json().catch(() => ({}));
                            throw new Error(err.error || 'Erreur envoi');
                        }
                        const data = await response.json();
                        const msg = data.message;
                        if (msg) {
                            msg.sender_id = msg.sender_id || msg.sender?.id;
                            msg.user_id = msg.sender_id;
                            this.messages.push(msg);
                        }
                        this.pendingImages.forEach(p => URL.revokeObjectURL(p.preview));
                        this.pendingImages = [];
                        this.newMessage = '';
                        this.$nextTick(() => this.scrollToBottom());
                    } catch (error) {
                        console.error('Error uploading images:', error);
                        alert(error.message || 'Impossible d\'envoyer les images.');
                    }
                },

                async toggleVoiceRecord() {
                    if (this.isRecording) {
                        this.stopVoiceRecord();
                        return;
                    }
                    try {
                        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                        this.mediaRecorder = new MediaRecorder(stream);
                        this.audioChunks = [];
                        this.mediaRecorder.ondataavailable = (e) => e.data.size && this.audioChunks.push(e.data);
                        this.mediaRecorder.onstop = () => {
                            stream.getTracks().forEach(t => t.stop());
                            this.sendVoiceMessage();
                        };
                        this.mediaRecorder.start();
                        this.isRecording = true;
                    } catch (err) {
                        console.error('Microphone error:', err);
                        alert('Accés au micro refusé ou indisponible.');
                    }
                },

                stopVoiceRecord() {
                    if (this.mediaRecorder && this.mediaRecorder.state !== 'inactive') {
                        this.mediaRecorder.stop();
                    }
                    this.isRecording = false;
                },

                async sendVoiceMessage() {
                    if (!this.selectedConversation || this.audioChunks.length === 0) return;
                    const blob = new Blob(this.audioChunks, { type: 'audio/webm' });
                    const formData = new FormData();
                    formData.append('files[]', blob, 'message-vocal.webm');
                    const csrf = document.querySelector('meta[name="csrf-token"]').content;
                    try {
                        const response = await fetch(`/messaging/api/conversations/${this.selectedConversation.id}/attachments`, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': csrf },
                            body: formData
                        });
                        if (!response.ok) {
                            const err = await response.json().catch(() => ({}));
                            throw new Error(err.error || 'Erreur envoi vocal');
                        }
                        const data = await response.json();
                        const msg = data.message;
                        if (msg) {
                            msg.sender_id = msg.sender_id || msg.sender?.id;
                            msg.user_id = msg.sender_id;
                            this.messages.push(msg);
                        }
                        this.$nextTick(() => this.scrollToBottom());
                    } catch (error) {
                        console.error('Error sending voice:', error);
                        alert(error.message || 'Impossible d\'envoyer le message vocal.');
                    }
                    this.audioChunks = [];
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

                isAudioAtt(att) {
                    if (!att) return false;
                    if (att.is_audio) return true;
                    if (att.type && att.type.startsWith('audio/')) return true;
                    return (att.name && /\.(webm|ogg|mp3|m4a|wav|mp4|weba)$/i.test(att.name));
                },

                playPauseVoice(att) {
                    if (this.voicePlayingId === att.id) {
                        this.voiceAudio.pause();
                        this.voicePlayingId = null;
                        return;
                    }
                    this.voicePlayingId = att.id;
                    this.voiceAudio.src = att.url;
                    this.voiceAudio.play().catch(() => { this.voicePlayingId = null; });
                },

                formatVoiceDuration(seconds) {
                    if (seconds === undefined || seconds === null || !isFinite(seconds)) return '0:00';
                    const m = Math.floor(seconds / 60);
                    const s = Math.floor(seconds % 60);
                    return m + ':' + (s < 10 ? '0' : '') + s;
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

    @push('scripts')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
    <script nonce="{{ $cspNonce ?? '' }}" src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <script nonce="{{ $cspNonce ?? '' }}" src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js" crossorigin="anonymous"></script>
    @endpush
</x-layouts.admin>
