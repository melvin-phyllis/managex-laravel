<x-layouts.employee>
    <style>
        [x-cloak] { display: none !important; }
        .no-select { user-select: none; -webkit-user-select: none; }

        /* Messaging Layout */
        .msg-sidebar {
            background: linear-gradient(180deg, #1B3C35 0%, #1a3530 100%);
            flex-shrink: 0;
            overflow: hidden;
        }
        .msg-conv-item {
            border-left: 3px solid transparent;
            transition: all 0.2s ease;
        }
        .msg-conv-item:hover { background: rgba(255,255,255,0.07); }
        .msg-conv-item.active {
            background: rgba(196,219,246,0.15);
            border-left-color: #C4DBF6;
        }

        /* Chat background – subtle pattern */
        .chat-messages-area {
            background-color: #f0f2f5;
            background-image: url("data:image/svg+xml,%3Csvg width='64' height='64' viewBox='0 0 64 64' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%231B3C35' fill-opacity='0.03'%3E%3Cpath d='M32 32m-4 0a4 4 0 1 1 8 0 4 4 0 0 1-8 0'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        /* Message bubbles */
        .bubble-out {
            background: linear-gradient(135deg, #1B3C35 0%, #2D5A4E 100%);
            color: white;
            border-radius: 18px 18px 4px 18px;
            box-shadow: 0 2px 8px rgba(27,60,53,0.25);
        }
        .bubble-in {
            background: white;
            color: #111827;
            border-radius: 18px 18px 18px 4px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        }

        /* Input area */
        .msg-input {
            background: white;
            border: 1.5px solid #e5e7eb;
            border-radius: 24px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .msg-input:focus-within {
            border-color: #1B3C35;
            box-shadow: 0 0 0 3px rgba(27,60,53,0.1);
        }

        /* Resizer */
        .resizer {
            width: 4px; background: transparent;
            cursor: col-resize; flex-shrink: 0;
            transition: background-color 0.2s;
        }
        .resizer:hover, .resizer.resizing { background: rgba(27,60,53,0.2); }

        /* Info panel */
        .msg-info-panel {
            border-left: 1px solid #e5e7eb;
            background: #fafafa;
        }

        /* Scrollbar styling */
        .slim-scroll::-webkit-scrollbar { width: 4px; }
        .slim-scroll::-webkit-scrollbar-track { background: transparent; }
        .slim-scroll::-webkit-scrollbar-thumb { background: rgba(27,60,53,0.2); border-radius: 2px; }

        .voice-wave-bar { min-height: 8px; }

        /* Avatar gradient */
        .avatar-gradient {
            background: linear-gradient(135deg, #1B3C35, #3D7A6A);
        }
        .avatar-gradient-gold {
            background: linear-gradient(135deg, #C8A96E, #B8955A);
        }
    </style>

    <div x-data="messagingApp()" x-init="init()">
        <div class="h-[calc(100vh-6rem)] flex rounded-2xl shadow-sm border border-gray-200 overflow-hidden"
             :class="{ 'no-select': isResizing }">

        <!-- Sidebar - Liste des conversations -->
        <div class="msg-sidebar flex flex-col flex-shrink-0 overflow-hidden"
             :class="{ 'hidden': selectedConversation && !isDesktop, 'flex': !selectedConversation || isDesktop }"
             :style="isDesktop ? 'width: ' + sidebarWidth + 'px' : 'width: 100%'"
             x-ref="sidebar">

            <!-- Sidebar Header -->
            <div class="px-5 pt-5 pb-4">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-xl font-bold text-white">Messages</h2>
                        <p class="text-xs text-white/50 mt-0.5" x-text="conversations.length + ' conversations'"></p>
                    </div>
                    <button @click="showNewConversation = true"
                            class="w-9 h-9 flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/20 text-white transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </button>
                </div>
                <!-- Search -->
                <div class="relative">
                    <svg class="w-4 h-4 text-white/40 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text"
                           x-model="searchQuery"
                           @input.debounce.300ms="filterConversations()"
                           placeholder="Rechercher..."
                           class="w-full pl-9 pr-4 py-2.5 bg-white/10 border border-white/10 rounded-xl text-sm text-white placeholder-white/40 focus:outline-none focus:bg-white/15 focus:border-white/20 transition-all">
                </div>
            </div>

            <!-- Tabs -->
            <div class="flex px-4 pb-3 gap-1">
                <button @click="activeTab = 'all'"
                        :class="activeTab === 'all' ? 'bg-white/15 text-white' : 'text-white/50 hover:text-white/80 hover:bg-white/5'"
                        class="flex-1 py-1.5 text-xs font-medium rounded-lg transition-all">Tous</button>
                <button @click="activeTab = 'direct'"
                        :class="activeTab === 'direct' ? 'bg-white/15 text-white' : 'text-white/50 hover:text-white/80 hover:bg-white/5'"
                        class="flex-1 py-1.5 text-xs font-medium rounded-lg transition-all">Directs</button>
                <button @click="activeTab = 'group'"
                        :class="activeTab === 'group' ? 'bg-white/15 text-white' : 'text-white/50 hover:text-white/80 hover:bg-white/5'"
                        class="flex-1 py-1.5 text-xs font-medium rounded-lg transition-all">Groupes</button>
            </div>

            <!-- Conversation List -->
            <div class="flex-1 overflow-y-auto slim-scroll">
                <!-- Loading -->
                <template x-if="loading">
                    <div class="flex items-center justify-center h-32">
                        <div class="w-6 h-6 border-2 border-white/20 border-t-white rounded-full animate-spin"></div>
                    </div>
                </template>

                <template x-for="conv in filteredConversations" :key="conv.id">
                    <div @click="selectConversation(conv)"
                         :class="selectedConversation?.id === conv.id ? 'msg-conv-item active' : 'msg-conv-item'"
                         class="px-4 py-3 cursor-pointer">
                        <div class="flex items-center gap-3">
                            <!-- Avatar -->
                            <div class="relative flex-shrink-0">
                                <div class="w-11 h-11 rounded-2xl avatar-gradient flex items-center justify-center text-white font-bold text-sm shadow-md">
                                    <span x-text="(conv.name || 'C').charAt(0).toUpperCase()"></span>
                                </div>
                                <template x-if="conv.is_pinned">
                                    <div class="absolute -top-1 -right-1 w-4 h-4 bg-amber-400 rounded-full flex items-center justify-center shadow">
                                        <svg class="w-2.5 h-2.5 text-amber-900" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </div>
                                </template>
                            </div>
                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <h3 class="font-semibold text-white text-sm truncate" x-text="conv.name || 'Conversation'"></h3>
                                    <span class="text-[11px] text-white/40 flex-shrink-0" x-text="formatTime(conv.last_message_at)"></span>
                                </div>
                                <div class="flex items-center justify-between mt-0.5">
                                    <p class="text-xs text-white/50 truncate" x-text="conv.last_message || 'Aucun message'"></p>
                                    <template x-if="conv.unread_count > 0">
                                        <span class="ml-2 flex-shrink-0 min-w-[18px] h-[18px] px-1 rounded-full text-[10px] font-bold flex items-center justify-center text-white" style="background:#C8A96E;" x-text="conv.unread_count"></span>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <template x-if="!loading && filteredConversations.length === 0">
                    <div class="text-center py-12">
                        <div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-white/10 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <p class="text-sm text-white/40">Aucune conversation</p>
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

            <!-- Empty State -->
            <template x-if="!selectedConversation">
                <div class="flex flex-1 items-center justify-center bg-gradient-to-br from-gray-50 to-white">
                    <div class="text-center px-6">
                        <div class="w-20 h-20 mx-auto mb-4 rounded-3xl flex items-center justify-center" style="background:linear-gradient(135deg,#1B3C35,#3D7A6A);">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Vos messages</h3>
                        <p class="text-sm text-gray-400 mt-2 max-w-xs">Sélectionnez une conversation ou démarrez-en une nouvelle</p>
                        <button @click="showNewConversation = true" class="mt-5 px-5 py-2.5 text-white text-sm font-semibold rounded-xl transition-all hover:shadow-lg" style="background:linear-gradient(135deg,#1B3C35,#3D7A6A);">
                            Nouvelle conversation
                        </button>
                    </div>
                </div>
            </template>

            <template x-if="selectedConversation">
                <div class="flex-1 flex flex-col h-full">
                    <!-- Chat Header -->
                    <div class="px-5 py-3.5 border-b border-gray-100 bg-white flex items-center justify-between shadow-sm">
                        <div class="flex items-center gap-3 min-w-0">
                            <!-- Mobile back -->
                            <button @click="selectedConversation = null" class="md:hidden p-2 -ml-2 text-gray-500 hover:bg-gray-100 rounded-xl flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <!-- Avatar -->
                            <div class="w-10 h-10 rounded-2xl avatar-gradient flex items-center justify-center text-white font-bold flex-shrink-0 shadow-md">
                                <span x-text="(selectedConversation?.name || 'C').charAt(0).toUpperCase()"></span>
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-bold text-gray-900 truncate" x-text="selectedConversation?.name || 'Conversation'"></h3>
                                <p class="text-xs text-gray-400 mt-0.5" x-text="selectedConversation.type === 'direct' ? 'Message direct' : (selectedConversation.participants?.length || 0) + ' participants'"></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <button @click="togglePin()" class="p-2 rounded-xl transition-all hover:bg-gray-100"
                                    :class="selectedConversation.is_pinned ? 'text-amber-500' : 'text-gray-400 hover:text-gray-600'">
                                <svg class="w-5 h-5" :class="selectedConversation.is_pinned ? 'fill-current' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Messages -->
                    <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4 chat-messages-area slim-scroll" id="messagesContainer" x-ref="messagesContainer" @scroll="handleScroll">
                        <template x-if="loadingMessages">
                            <div class="flex items-center justify-center py-8">
                                <div class="w-7 h-7 border-2 border-[#1B3C35]/20 border-t-[#1B3C35] rounded-full animate-spin"></div>
                            </div>
                        </template>

                        <template x-for="(message, index) in messages" :key="message.id">
                            <div :class="(message.sender?.id || message.sender_id) === currentUserId ? 'flex justify-end' : 'flex justify-start items-end gap-2'">
                                <!-- Other user avatar -->
                                <template x-if="(message.sender?.id || message.sender_id) !== currentUserId">
                                    <div class="w-7 h-7 rounded-full avatar-gradient flex items-center justify-center text-white text-xs font-bold flex-shrink-0 mb-1">
                                        <span x-text="(message.sender?.name || '?').charAt(0).toUpperCase()"></span>
                                    </div>
                                </template>

                                <div :class="(message.sender?.id || message.sender_id) === currentUserId ? 'bubble-out' : 'bubble-in'"
                                     class="max-w-[78%] sm:max-w-sm px-4 py-2.5">
                                    <!-- Sender name for others in groups -->
                                    <template x-if="(message.sender?.id || message.sender_id) !== currentUserId && message.sender && selectedConversation.type === 'group'">
                                        <p class="text-xs font-semibold mb-1 opacity-70" x-text="message.sender.name"></p>
                                    </template>

                                    <!-- Attachments -->
                                    <template x-if="message.attachments && message.attachments.length">
                                        <div class="space-y-2 mt-1">
                                            <template x-for="att in message.attachments" :key="att.id">
                                                <div>
                                                    <template x-if="att.is_image">
                                                        <a :href="att.url" :data-lightbox="'chat-' + message.id" :data-title="att.name" class="block rounded-xl overflow-hidden max-w-xs hover:opacity-95 transition-opacity">
                                                            <img :src="att.url" :alt="att.name" class="max-h-56 w-full object-cover"/>
                                                        </a>
                                                    </template>
                                                    <template x-if="isAudioAtt(att)">
                                                        <div class="flex items-center gap-2 py-1.5 px-3 rounded-xl min-w-[200px] max-w-[260px]"
                                                             :class="(message.sender?.id || message.sender_id) === currentUserId ? 'bg-white/20' : 'bg-gray-100'">
                                                            <button type="button" @click="playPauseVoice(att)" class="flex-shrink-0 w-9 h-9 rounded-full flex items-center justify-center bg-emerald-500/80 text-white hover:bg-emerald-600 transition">
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
                                                        </div>
                                                    </template>
                                                    <template x-if="!att.is_image && !isAudioAtt(att)">
                                                        <a :href="att.download_url" class="inline-flex items-center gap-2 text-sm underline opacity-80 hover:opacity-100" x-text="att.name"></a>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    <!-- Text content -->
                                    <div class="text-sm leading-relaxed break-words mt-1" x-show="message.content" x-html="message.content_html || message.content"></div>

                                    <!-- Footer: time + check -->
                                    <div class="flex items-center justify-end gap-1 mt-1.5"
                                         :class="(message.sender?.id || message.sender_id) === currentUserId ? 'text-white/60' : 'text-gray-400'">
                                        <span class="text-[10px]" x-text="message.created_at_human || formatMessageTime(message.created_at)"></span>
                                        <template x-if="(message.sender?.id || message.sender_id) === currentUserId">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.7 7.3l-6.9 6.9-2.8-2.8-1.4 1.4 4.2 4.2 8.3-8.3z"/></svg>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Message Input -->
                    <div class="px-4 py-3 bg-white border-t border-gray-100">
                        <!-- Pending images preview -->
                        <div x-show="pendingImages.length > 0" class="flex gap-2 mb-3 overflow-x-auto pb-2">
                            <template x-for="(file, idx) in pendingImages" :key="idx">
                                <div class="relative flex-shrink-0">
                                    <img :src="file.preview" class="w-16 h-16 object-cover rounded-xl border border-gray-200"/>
                                    <button type="button" @click="removePendingImage(idx)" class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600 shadow">×</button>
                                </div>
                            </template>
                        </div>

                        <form @submit.prevent="sendMessageOrAttachments()" class="flex items-end gap-2">
                            <input type="file" x-ref="imageInput" @change="onImageSelected($event)" accept="image/*" multiple class="hidden"/>

                            <!-- Actions left -->
                            <div class="flex gap-1 pb-1.5">
                                <button type="button" @click="$refs.imageInput.click()" class="w-9 h-9 flex items-center justify-center text-gray-400 hover:text-[#1B3C35] hover:bg-gray-100 rounded-xl transition-all" title="Image">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                                <button type="button"
                                        @mousedown.prevent="startVoiceRecord()"
                                        @mouseup.prevent="stopVoiceRecord()"
                                        @mouseleave="isRecording && stopVoiceRecord()"
                                        @touchstart.prevent="startVoiceRecord()"
                                        @touchend.prevent="stopVoiceRecord()"
                                        :class="isRecording ? 'bg-red-500 text-white scale-110 animate-pulse' : 'text-gray-400 hover:text-[#1B3C35] hover:bg-gray-100'"
                                        class="w-9 h-9 flex items-center justify-center rounded-xl transition-all select-none"
                                        :title="isRecording ? 'Relâchez pour envoyer' : 'Maintenez pour enregistrer'">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                                    </svg>
                                </button>
                            </div>

                            <!-- Text input -->
                            <div class="flex-1 msg-input flex items-end px-4 py-2">
                                <textarea x-model="newMessage"
                                          @keydown.enter.meta="sendMessageOrAttachments()"
                                          @keydown.enter.ctrl="sendMessageOrAttachments()"
                                          placeholder="Écrivez votre message..."
                                          rows="1"
                                          class="flex-1 bg-transparent border-0 outline-none resize-none text-sm text-gray-800 placeholder-gray-400 max-h-32"
                                          x-ref="messageInput"></textarea>
                            </div>

                            <!-- Send button -->
                            <button type="submit"
                                    :disabled="!canSend()"
                                    class="w-10 h-10 flex items-center justify-center text-white rounded-2xl transition-all disabled:opacity-40 disabled:cursor-not-allowed hover:shadow-lg flex-shrink-0 mb-0.5"
                                    style="background:linear-gradient(135deg,#1B3C35,#3D7A6A);">
                                <svg class="w-5 h-5 rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
         @click.self="closeNewConversationModal()"
         @keydown.escape.window="closeNewConversationModal()">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
            <!-- Modal header -->
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Nouvelle conversation</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Démarrez un échange direct ou de groupe</p>
                </div>
                <button @click="closeNewConversationModal()" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form @submit.prevent="createConversation()" class="p-6 space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Type</label>
                    <div class="flex gap-2">
                        <button type="button" @click="newConversationType = 'direct'"
                                :class="newConversationType === 'direct' ? 'text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                                :style="newConversationType === 'direct' ? 'background:linear-gradient(135deg,#1B3C35,#3D7A6A)' : ''"
                                class="flex-1 py-2.5 text-sm font-medium rounded-xl transition-all">
                            Message direct
                        </button>
                        <button type="button" @click="newConversationType = 'group'"
                                :class="newConversationType === 'group' ? 'text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                                :style="newConversationType === 'group' ? 'background:linear-gradient(135deg,#1B3C35,#3D7A6A)' : ''"
                                class="flex-1 py-2.5 text-sm font-medium rounded-xl transition-all">
                            Groupe
                        </button>
                    </div>
                </div>
                <template x-if="newConversationType === 'group'">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nom du groupe</label>
                        <input type="text" x-model="newConversationName"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#1B3C35] focus:ring-2 focus:ring-[#1B3C35]/10 focus:bg-white transition-all"
                               placeholder="Ex: Équipe Marketing...">
                    </div>
                </template>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Participants</label>
                    <select x-model="selectedParticipants" multiple
                            class="w-full border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#1B3C35] focus:ring-2 focus:ring-[#1B3C35]/10 transition-all" size="5">
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" class="py-2 px-3">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-2">Maintenez Ctrl pour sélectionner plusieurs</p>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="closeNewConversationModal()"
                            class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-200 transition-colors">
                        Annuler
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2.5 text-white rounded-xl text-sm font-semibold hover:shadow-lg transition-all"
                            style="background:linear-gradient(135deg,#1B3C35,#3D7A6A);">
                        Créer
                    </button>
                </div>
            </form>
        </div>
    </div>


    </div> <!-- Close x-data wrapper -->

    <script nonce="{{ $cspNonce ?? '' }}">
        function messagingApp() {
            const baseUrl = '{{ url("/") }}';
            return {
                conversations: [],
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

                // Voice player (style WhatsApp)
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

                async init() {
                    await this.loadConversations();
                    this.$watch('activeTab', () => this.filterConversations());

                    // Load saved sidebar width
                    const savedWidth = localStorage.getItem('messaging_sidebar_width_employee');
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

                    // Voice player: single Audio instance
                    this.voiceAudio = new Audio();
                    this.voiceAudio.addEventListener('timeupdate', () => {
                        this.voiceCurrentTime = this.voiceAudio.currentTime;
                    });
                    this.voiceAudio.addEventListener('loadedmetadata', () => {
                        this.voiceDuration = this.voiceAudio.duration;
                        if (this.voicePlayingId) this.voiceDurations[this.voicePlayingId] = this.voiceAudio.duration;
                    });
                    this.voiceAudio.addEventListener('ended', () => {
                        this.voicePlayingId = null;
                        this.voiceCurrentTime = 0;
                    });
                    this.voiceAudio.addEventListener('pause', () => {
                        if (this.voiceAudio.ended || this.voiceAudio.currentTime >= this.voiceAudio.duration) return;
                        this.voicePlayingId = null;
                    });

                    // Cleanup on page unload
                    window.addEventListener('beforeunload', () => this.stopPolling());
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
                        localStorage.setItem('messaging_sidebar_width_employee', this.sidebarWidth.toString());
                    }
                },

                startPolling() {
                    this._failCount = 0;

                    // Pause polling when offline, resume when online
                    window.addEventListener('online', () => {
                        this._failCount = 0;
                        if (!this.pollingInterval) this._startIntervals();
                    });
                    window.addEventListener('offline', () => this._clearIntervals());

                    if (navigator.onLine) this._startIntervals();
                },

                _startIntervals() {
                    if (this.pollingInterval) return;

                    // Poll for new messages every 3 seconds when a conversation is selected
                    this.pollingInterval = setInterval(() => {
                        if (this.selectedConversation && !this.loadingMessages && navigator.onLine) {
                            this.pollNewMessages();
                        }
                    }, 3000);

                    // Poll for conversation updates every 10 seconds
                    this.conversationPollingInterval = setInterval(() => {
                        if (navigator.onLine) this.pollConversations();
                    }, 10000);
                },

                _clearIntervals() {
                    if (this.pollingInterval) { clearInterval(this.pollingInterval); this.pollingInterval = null; }
                    if (this.conversationPollingInterval) { clearInterval(this.conversationPollingInterval); this.conversationPollingInterval = null; }
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

                fetchWithTimeout(url, options = {}, timeoutMs = 15000) {
                    const controller = new AbortController();
                    const id = setTimeout(() => controller.abort(), timeoutMs);
                    return fetch(url, { ...options, signal: controller.signal }).finally(() => clearTimeout(id));
                },

                async pollNewMessages() {
                    if (!this.selectedConversation) return;

                    try {
                        const lastId = this.messages.length > 0 ? this.messages[this.messages.length - 1].id : 0;
                        const response = await this.fetchWithTimeout(`${baseUrl}/messaging/api/conversations/${this.selectedConversation.id}/messages?after=${lastId}`);
                        const data = await response.json();

                        if (data.messages && data.messages.length > 0) {
                            const existingIds = new Set(this.messages.map(m => m.id));
                            const newMessages = data.messages.filter(m => !existingIds.has(m.id));

                            if (newMessages.length > 0) {
                                this.messages.push(...newMessages);
                                this.$nextTick(() => this.scrollToBottom());

                                // Mark as read
                                await this.fetchWithTimeout(`${baseUrl}/messaging/api/conversations/${this.selectedConversation.id}/read`, {
                                    method: 'POST',
                                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
                                }, 5000);
                            }
                        }
                    } catch (error) {
                        this._failCount = (this._failCount || 0) + 1;
                        if (this._failCount <= 1) {
                            console.warn('Chat polling: network issue, will retry silently.');
                        }
                    }
                },

                async pollConversations() {
                    try {
                        const response = await this.fetchWithTimeout(`${baseUrl}/messaging/api/conversations`);
                        const newConversations = await response.json();

                        const selectedId = this.selectedConversation?.id;
                        this.conversations = newConversations;
                        this.filterConversations();

                        if (selectedId) {
                            const updated = this.conversations.find(c => c.id === selectedId);
                            if (updated) {
                                this.selectedConversation = {...this.selectedConversation, ...updated};
                            }
                        }
                    } catch (error) {
                        this._failCount = (this._failCount || 0) + 1;
                        if (this._failCount <= 1) {
                            console.warn('Chat polling: network issue, will retry silently.');
                        }
                    }
                },

                async loadConversations() {
                    this.loading = true;
                    try {
                        const response = await fetch(`${baseUrl}/messaging/api/conversations`);
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
                        filtered = filtered.filter(c => c.name?.toLowerCase().includes(query));
                    }

                    this.filteredConversations = filtered;
                },

                async selectConversation(conv) {
                    this.selectedConversation = conv;
                    this.loadingMessages = true;
                    try {
                        const response = await fetch(`${baseUrl}/messaging/api/conversations/${conv.id}/messages`);
                        const data = await response.json();
                        this.messages = data.messages;

                        await fetch(`${baseUrl}/messaging/api/conversations/${conv.id}/read`, { method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'} });
                        conv.unread_count = 0;

                        this.$nextTick(() => this.scrollToBottom());
                    } catch (error) {
                        console.error('Error loading messages:', error);
                    }
                    this.loadingMessages = false;
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
                        const response = await fetch(`${baseUrl}/messaging/api/conversations/${this.selectedConversation.id}/messages`, {
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
                        const preview = URL.createObjectURL(file);
                        this.pendingImages.push({ file, preview });
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

                    try {
                        const response = await fetch(`${baseUrl}/messaging/api/conversations/${this.selectedConversation.id}/attachments`, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: formData
                        });
                        if (!response.ok) {
                            const err = await response.json().catch(() => ({}));
                            throw new Error(err.error || 'Erreur envoi');
                        }
                        const data = await response.json();
                        this.messages.push(data.message);
                        this.pendingImages.forEach(p => URL.revokeObjectURL(p.preview));
                        this.pendingImages = [];
                        this.newMessage = '';
                        this.$nextTick(() => this.scrollToBottom());
                    } catch (error) {
                        console.error('Error uploading images:', error);
                        alert(error.message || 'Impossible d\'envoyer les images.');
                    }
                },

                async startVoiceRecord() {
                    if (this.isRecording) return;
                    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                        alert('Votre navigateur ne supporte pas l\'enregistrement audio.');
                        return;
                    }
                    try {
                        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                        this.mediaRecorder = new MediaRecorder(stream);
                        this.audioChunks = [];
                        this.mediaRecorder.ondataavailable = (e) => e.data.size && this.audioChunks.push(e.data);
                        this.mediaRecorder.onstop = () => {
                            stream.getTracks().forEach(t => t.stop());
                            if (this.audioChunks.length > 0) {
                                this.sendVoiceMessage();
                            }
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

                    try {
                        const response = await fetch(`${baseUrl}/messaging/api/conversations/${this.selectedConversation.id}/attachments`, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: formData
                        });
                        if (!response.ok) {
                            const err = await response.json().catch(() => ({}));
                            throw new Error(err.error || 'Erreur envoi vocal');
                        }
                        const data = await response.json();
                        this.messages.push(data.message);
                        this.$nextTick(() => this.scrollToBottom());
                    } catch (error) {
                        console.error('Error sending voice:', error);
                        alert(error.message || 'Impossible d\'envoyer le message vocal.');
                    }
                    this.audioChunks = [];
                },

                async createConversation() {
                    if (this.selectedParticipants.length === 0) {
                        alert('Veuillez sélectionner au moins un participant.');
                        return;
                    }

                    try {
                        const response = await fetch(`${baseUrl}/messaging/api/conversations`, {
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
                        this.closeNewConversationModal();

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
                        const response = await fetch(`${baseUrl}/messaging/api/conversations/${this.selectedConversation.id}/pin`, {
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

                scrollToBottom() {
                    const container = this.$refs.messagesContainer;
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                },

                handleScroll(e) {
                    // Load more messages when scrolling to top
                    if (e.target.scrollTop === 0 && this.messages.length > 0) {
                        // TODO: Implement infinite scroll for older messages
                    }
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
                    if (!dateStr) return '';
                    const date = new Date(dateStr);
                    return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
                }
            };
        }
    </script>

    @push('scripts')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
    <script nonce="{{ $cspNonce ?? '' }}" src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <script nonce="{{ $cspNonce ?? '' }}" src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js" crossorigin="anonymous"></script>
    @endpush
</x-layouts.employee>
