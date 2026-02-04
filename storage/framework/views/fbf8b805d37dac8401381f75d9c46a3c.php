<?php if (isset($component)) { $__componentOriginal09d149b94538c2315f503a5e890f2640 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal09d149b94538c2315f503a5e890f2640 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.employee','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.employee'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
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
        /* Chat area style type WhatsApp */
        .chat-messages-area {
            background-color: #e5ddd5;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23d4cdc4' fill-opacity='0.4'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .voice-wave-bar { min-height: 8px; }
    </style>

    <div x-data="messagingApp()" x-init="init()">
        <div class="h-[calc(100vh-8rem)] flex bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden"
             :class="{ 'no-select': isResizing }">

        <!-- Sidebar - Liste des conversations -->
        <div class="border-r border-gray-200 flex flex-col bg-gray-50/50 flex-shrink-0 overflow-hidden"
             :class="{ 'hidden': selectedConversation && !isDesktop, 'flex': !selectedConversation || isDesktop }"
             :style="isDesktop ? 'width: ' + sidebarWidth + 'px' : 'width: 100%'"
             x-ref="sidebar">
            <!-- Header -->
            <div class="p-4 border-b border-gray-200 bg-white">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-lg font-bold text-gray-800">Messages</h2>
                    <button @click="showNewConversation = true"
                            class="p-2 bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl hover:shadow-md transition-all">
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
                           class="w-full pl-10 pr-4 py-2.5 bg-gray-100 border-0 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Tabs -->
            <div class="flex border-b border-gray-200 bg-white px-1">
                <button @click="activeTab = 'all'"
                        :class="activeTab === 'all' ? 'bg-blue-50 text-blue-600 border-blue-600' : 'border-transparent text-gray-500 hover:bg-gray-100'"
                        class="flex-1 py-2.5 text-sm font-medium rounded-t-lg border-b-2 transition-all">
                    Tous
                </button>
                <button @click="activeTab = 'direct'"
                        :class="activeTab === 'direct' ? 'bg-blue-50 text-blue-600 border-blue-600' : 'border-transparent text-gray-500 hover:bg-gray-100'"
                        class="flex-1 py-2.5 text-sm font-medium rounded-t-lg border-b-2 transition-all">
                    Directs
                </button>
                <button @click="activeTab = 'group'"
                        :class="activeTab === 'group' ? 'bg-blue-50 text-blue-600 border-blue-600' : 'border-transparent text-gray-500 hover:bg-gray-100'"
                        class="flex-1 py-2.5 text-sm font-medium rounded-t-lg border-b-2 transition-all">
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
                         :class="selectedConversation?.id === conv.id ? 'bg-blue-50/80 border-l-4 border-blue-500' : 'hover:bg-white border-l-4 border-transparent'"
                         class="p-4 cursor-pointer transition-all">
                        <div class="flex items-start gap-3">
                            <!-- Avatar -->
                            <div class="relative flex-shrink-0">
                                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-semibold shadow-sm">
                                    <span x-text="(conv.name || 'C').charAt(0).toUpperCase()"></span>
                                </div>
                                <template x-if="conv.is_pinned">
                                    <div class="absolute -top-1 -right-1 bg-yellow-400 rounded-full p-0.5 shadow-sm">
                                        <svg class="w-3 h-3 text-yellow-800" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </div>
                                </template>
                            </div>
                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <h3 class="font-medium text-gray-800 truncate" x-text="conv.name || 'Conversation'"></h3>
                                    <span class="text-xs text-gray-400 ml-2 flex-shrink-0" x-text="formatTime(conv.last_message_at)"></span>
                                </div>
                                <p class="text-sm text-gray-500 truncate mt-0.5" x-text="conv.last_message || 'Aucun message'"></p>
                            </div>
                            <!-- Unread Badge -->
                            <template x-if="conv.unread_count > 0">
                                <span class="bg-gradient-to-r from-blue-500 to-blue-600 text-white text-xs font-bold px-2 py-0.5 rounded-full shadow-sm flex-shrink-0" x-text="conv.unread_count"></span>
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

        <!-- Resizer Handle (desktop only) -->
        <div class="resizer hidden md:block"
             :class="{ 'resizing': isResizing }"
             @mousedown="startResize($event)"
             x-show="isDesktop"
             x-ref="resizer"></div>

        <!-- Main Chat Area -->
        <div class="flex-1 flex flex-col min-w-0"
             :class="{ 'hidden': !selectedConversation && !isDesktop, 'flex': selectedConversation || isDesktop }">
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

            <template x-if="selectedConversation">
                <div class="flex-1 flex flex-col h-full">
                    <!-- Chat Header -->
                    <div class="px-4 sm:px-6 py-4 border-b border-gray-200 bg-white flex items-center justify-between">
                        <div class="flex items-center gap-3 min-w-0">
                            <!-- Bouton retour (mobile uniquement) -->
                            <button @click="selectedConversation = null"
                                    class="md:hidden p-2 -ml-2 text-gray-600 hover:bg-gray-100 rounded-lg flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-semibold flex-shrink-0">
                                <span x-text="(selectedConversation?.name || 'C').charAt(0).toUpperCase()"></span>
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-semibold text-gray-900 truncate" x-text="selectedConversation?.name || 'Conversation'"></h3>
                                <p class="text-xs text-gray-500" x-text="selectedConversation.type === 'direct' ? 'Message direct' : (selectedConversation.participants?.length || 0) + ' participants'"></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1 sm:gap-2 flex-shrink-0">
                            <button @click="togglePin()" class="p-2 text-gray-400 hover:text-yellow-500 hover:bg-gray-100 rounded-lg transition-colors">
                                <svg class="w-5 h-5" :class="selectedConversation.is_pinned ? 'text-yellow-500 fill-current' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Messages -->
                    <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-3 chat-messages-area" id="messagesContainer" x-ref="messagesContainer" @scroll="handleScroll">
                        <template x-if="loadingMessages">
                            <div class="flex items-center justify-center py-4">
                                <svg class="animate-spin h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </template>

                        <template x-for="(message, index) in messages" :key="message.id">
                            <div :class="(message.sender?.id || message.sender_id) === currentUserId ? 'flex justify-end' : 'flex justify-start'">
                                <div :class="[
                                    (message.sender?.id || message.sender_id) === currentUserId ? 'bg-[#dcf8c6] text-gray-900 rounded-tl-xl rounded-tr-xl rounded-bl-xl rounded-br-md' : 'bg-white text-gray-900 rounded-tl-xl rounded-tr-xl rounded-br-xl rounded-bl-md',
                                    'max-w-[85%] sm:max-w-md px-3 py-2 shadow-md'
                                ]">
                                    <!-- Sender name for others -->
                                    <template x-if="(message.sender?.id || message.sender_id) !== currentUserId && message.sender">
                                        <p class="text-xs font-semibold text-emerald-700 mb-0.5" x-text="message.sender.name"></p>
                                    </template>

                                    <!-- Attachments (images, audio, files) -->
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
                                                             :class="(message.sender?.id || message.sender_id) === currentUserId ? 'bg-white/60' : 'bg-gray-100'">
                                                            <button type="button" @click="playPauseVoice(att)" class="flex-shrink-0 w-9 h-9 rounded-full flex items-center justify-center transition"
                                                                    :class="(message.sender?.id || message.sender_id) === currentUserId ? 'bg-emerald-500/80 text-white hover:bg-emerald-600' : 'bg-emerald-500/80 text-white hover:bg-emerald-600'">
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
                                    <!-- Content (caption under image or text) -->
                                    <div class="text-sm break-words mt-1" x-show="message.content" x-html="message.content_html || message.content"></div>

                                    <!-- Footer: time + check (style WhatsApp) -->
                                    <div class="flex items-center justify-end gap-1 mt-0.5" :class="(message.sender?.id || message.sender_id) === currentUserId ? 'text-emerald-700/80' : 'text-gray-500'">
                                        <span class="text-[11px]" x-text="message.created_at_human || formatMessageTime(message.created_at)"></span>
                                        <template x-if="(message.sender?.id || message.sender_id) === currentUserId">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.7 7.3l-6.9 6.9-2.8-2.8-1.4 1.4 4.2 4.2 8.3-8.3zM12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2z"/></svg>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Message Input -->
                    <div class="p-3 sm:p-4 border-t border-gray-100 bg-white">
                        <!-- Preview images é  envoyer -->
                        <div x-show="pendingImages.length > 0" class="flex gap-2 mb-3 overflow-x-auto pb-2">
                            <template x-for="(file, idx) in pendingImages" :key="idx">
                                <div class="relative flex-shrink-0">
                                    <img :src="file.preview" class="w-16 h-16 object-cover rounded-lg border border-gray-200"/>
                                    <button type="button" @click="removePendingImage(idx)" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600">é—</button>
                                </div>
                            </template>
                        </div>
                        <form @submit.prevent="sendMessageOrAttachments()" class="flex items-end gap-2 sm:gap-3">
                            <input type="file" x-ref="imageInput" @change="onImageSelected($event)" accept="image/*" multiple class="hidden"/>
                            <button type="button" @click="$refs.imageInput.click()" class="p-3 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-colors flex-shrink-0" title="Envoyer une image">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </button>
                            <button type="button" @click="toggleVoiceRecord()" :class="isRecording ? 'bg-red-500 hover:bg-red-600 text-white' : 'text-gray-500 hover:text-blue-600 hover:bg-blue-50'" class="p-3 rounded-xl transition-colors flex-shrink-0" :title="isRecording ? 'Arréªter et envoyer' : 'Message vocal'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v3m0 0V6a7 7 0 0114 0v3m-7 4a7 7 0 009.5 1.5"/>
                                </svg>
                            </button>
                            <div class="flex-1">
                                <textarea x-model="newMessage"
                                          @keydown.enter.meta="sendMessageOrAttachments()"
                                          @keydown.enter.ctrl="sendMessageOrAttachments()"
                                          placeholder="écrivez votre message..."
                                          rows="1"
                                          class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl resize-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-all text-sm sm:text-base"
                                          x-ref="messageInput"></textarea>
                            </div>
                            <button type="submit"
                                    :disabled="!canSend()"
                                    class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl hover:shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed flex-shrink-0">
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
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
         @click.self="closeNewConversationModal()"
         @keydown.escape.window="closeNewConversationModal()">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-4 sm:p-6 max-h-[90vh] overflow-y-auto">
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
                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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

    <script nonce="<?php echo e($cspNonce ?? ''); ?>">
        function messagingApp() {
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
                currentUserId: <?php echo e(auth()->id()); ?>,
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

                fetchWithTimeout(url, options = {}, timeoutMs = 15000) {
                    const controller = new AbortController();
                    const id = setTimeout(() => controller.abort(), timeoutMs);
                    return fetch(url, { ...options, signal: controller.signal }).finally(() => clearTimeout(id));
                },

                async pollNewMessages() {
                    if (!this.selectedConversation) return;

                    try {
                        const lastId = this.messages.length > 0 ? this.messages[this.messages.length - 1].id : 0;
                        const response = await this.fetchWithTimeout(`/messaging/api/conversations/${this.selectedConversation.id}/messages?after=${lastId}`);
                        const data = await response.json();

                        if (data.messages && data.messages.length > 0) {
                            const existingIds = new Set(this.messages.map(m => m.id));
                            const newMessages = data.messages.filter(m => !existingIds.has(m.id));

                            if (newMessages.length > 0) {
                                this.messages.push(...newMessages);
                                this.$nextTick(() => this.scrollToBottom());

                                // Mark as read
                                await this.fetchWithTimeout(`/messaging/api/conversations/${this.selectedConversation.id}/read`, {
                                    method: 'POST',
                                    headers: {'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'}
                                }, 5000);
                            }
                        }
                    } catch (error) {
                        console.error('Error polling messages:', error);
                    }
                },

                async pollConversations() {
                    try {
                        const response = await this.fetchWithTimeout('/messaging/api/conversations');
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
                        filtered = filtered.filter(c => c.name?.toLowerCase().includes(query));
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

                        await fetch(`/messaging/api/conversations/${conv.id}/read`, { method: 'POST', headers: {'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'} });
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
                        const response = await fetch(`/messaging/api/conversations/${this.selectedConversation.id}/messages`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
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
                        const response = await fetch(`/messaging/api/conversations/${this.selectedConversation.id}/attachments`, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
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

                    try {
                        const response = await fetch(`/messaging/api/conversations/${this.selectedConversation.id}/attachments`, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
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
                        const response = await fetch('/messaging/api/conversations', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
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
                        const response = await fetch(`/messaging/api/conversations/${this.selectedConversation.id}/pin`, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }
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

    <?php $__env->startPush('scripts'); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
    <script nonce="<?php echo e($cspNonce ?? ''); ?>" src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <script nonce="<?php echo e($cspNonce ?? ''); ?>" src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js" crossorigin="anonymous"></script>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal09d149b94538c2315f503a5e890f2640)): ?>
<?php $attributes = $__attributesOriginal09d149b94538c2315f503a5e890f2640; ?>
<?php unset($__attributesOriginal09d149b94538c2315f503a5e890f2640); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal09d149b94538c2315f503a5e890f2640)): ?>
<?php $component = $__componentOriginal09d149b94538c2315f503a5e890f2640; ?>
<?php unset($__componentOriginal09d149b94538c2315f503a5e890f2640); ?>
<?php endif; ?>
<?php /**PATH D:\ManageX\resources\views/messaging/index.blade.php ENDPATH**/ ?>