<x-dynamic-component :component="auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.employee'">
    <div class="p-6" x-data="{ showSendConfirmModal: false }">
        @php
            $statusLabels = [
                'pending' => 'En attente',
                'reviewed' => 'Vu',
                'shortlisted' => 'Preselectionne',
                'rejected' => 'Rejete',
            ];
            $statusClasses = [
                'pending' => 'bg-amber-100 text-amber-800',
                'reviewed' => 'bg-blue-100 text-blue-800',
                'shortlisted' => 'bg-green-100 text-green-800',
                'rejected' => 'bg-red-100 text-red-800',
            ];
        @endphp

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Lecture candidature</h1>
                <p class="text-sm text-gray-500 mt-1">Vue detaillee de la demande de stage.</p>
            </div>
            <a href="{{ route('admin.stage-requests.index') }}" class="inline-flex items-center justify-center px-3 py-2 rounded-lg border border-gray-300 text-sm text-gray-700 hover:bg-gray-50">
                Retour a la liste
            </a>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-5">
            <div class="xl:col-span-8 space-y-5">
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-[#F0F5F3] to-white">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-full flex items-center justify-center text-white font-bold text-sm" style="background: linear-gradient(135deg, #1B3C35, #2D5A4E);">
                                    {{ strtoupper(substr($stageRequest->full_name, 0, 2)) }}
                                </div>
                                <div>
                                    <h2 class="font-semibold text-gray-900 text-lg">{{ $stageRequest->full_name }}</h2>
                                    <p class="text-sm text-gray-600 break-all">{{ $stageRequest->email }}</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $statusClasses[$stageRequest->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabels[$stageRequest->status] ?? $stageRequest->status }}
                            </span>
                        </div>
                    </div>

                    <div class="p-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div class="rounded-xl border border-gray-100 p-3">
                                <p class="text-gray-500 text-xs mb-1">Telephone</p>
                                <p class="font-medium text-gray-900">{{ $stageRequest->phone ?: '-' }}</p>
                            </div>
                            <div class="rounded-xl border border-gray-100 p-3">
                                <p class="text-gray-500 text-xs mb-1">Date reception</p>
                                <p class="font-medium text-gray-900">{{ $stageRequest->created_at?->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="rounded-xl border border-gray-100 p-3">
                                <p class="text-gray-500 text-xs mb-1">Ecole</p>
                                <p class="font-medium text-gray-900">{{ $stageRequest->school ?: '-' }}</p>
                            </div>
                            <div class="rounded-xl border border-gray-100 p-3">
                                <p class="text-gray-500 text-xs mb-1">Niveau</p>
                                <p class="font-medium text-gray-900">{{ $stageRequest->level ?: '-' }}</p>
                            </div>
                            <div class="rounded-xl border border-gray-100 p-3 md:col-span-2">
                                <p class="text-gray-500 text-xs mb-1">Poste souhaite</p>
                                <p class="font-medium text-gray-900">{{ $stageRequest->desired_role ?: '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5">
                    <h3 class="font-semibold text-gray-900 mb-3">Message du candidat</h3>
                    <div class="text-sm text-gray-700 whitespace-pre-wrap leading-7 bg-slate-50 border border-slate-100 rounded-xl p-4">
                        {{ $stageRequest->message ?: '-' }}
                    </div>
                </div>
            </div>

            <div class="xl:col-span-4 space-y-5">
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5">
                    <h3 class="font-semibold text-gray-900 mb-3">Traitement RH</h3>
                    <form method="POST" action="{{ route('admin.stage-requests.update-status', $stageRequest) }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Decision finale</label>
                            <select name="final_status" class="border border-gray-200 rounded-lg text-sm px-3 py-2 bg-white w-full">
                                <option value="">-- Non definie --</option>
                                <option value="retained" @selected(old('final_status', $stageRequest->final_status)==='retained')>Retenu</option>
                                <option value="waitlist" @selected(old('final_status', $stageRequest->final_status)==='waitlist')>Liste d'attente</option>
                                <option value="rejected" @selected(old('final_status', $stageRequest->final_status)==='rejected')>Rejete</option>
                            </select>
                        </div>

                        <label class="block text-xs text-gray-500 mb-1">Note interne</label>
                        <textarea name="admin_note" rows="3" placeholder="Ajouter une note RH..." class="border border-gray-200 rounded-lg text-sm px-3 py-2 w-full">{{ old('admin_note', $stageRequest->admin_note) }}</textarea>
                        <button type="submit" class="w-full px-3 py-2 rounded-lg text-white text-sm font-medium" style="background: linear-gradient(135deg, #1B3C35, #2D5A4E);">
                            Enregistrer
                        </button>
                    </form>

                    @if($stageRequest->final_status === 'retained')
                        <form id="send-mail-form-show" method="POST" action="{{ route('admin.stage-requests.send-retained-mail', $stageRequest) }}" class="mt-3">
                            @csrf
                            <button type="button" @click="showSendConfirmModal = true" class="inline-flex w-full items-center justify-center px-3 py-2 rounded-lg border border-emerald-300 bg-emerald-50 text-emerald-700 text-sm font-medium">
                                {{ $stageRequest->retained_mail_sent_at ? 'Renvoyer mail' : 'Envoyer un mail' }}
                            </button>
                        </form>
                        @if($stageRequest->retained_mail_sent_at)
                            <p class="text-xs text-emerald-700 mt-2">Dernier envoi: {{ $stageRequest->retained_mail_sent_at->format('d/m/Y H:i') }}</p>
                        @endif
                    @endif
                </div>

                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold text-gray-900">Pieces jointes</h3>
                        <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700">{{ $stageRequest->attachments->count() }}</span>
                    </div>
                    @if($stageRequest->attachments->isEmpty())
                        <p class="text-sm text-gray-500">Aucune piece jointe.</p>
                    @else
                        <ul class="space-y-2">
                            @foreach($stageRequest->attachments as $file)
                                <li class="border border-gray-200 rounded-xl p-3 hover:bg-gray-50 transition"
                                    ondblclick="window.open('{{ route('admin.stage-requests.attachments.download', [$stageRequest, $file->id]) }}', '_blank')">
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-[#F0F5F3] text-[#1B3C35] flex items-center justify-center text-xs font-bold">
                                            {{ strtoupper(pathinfo($file->original_name, PATHINFO_EXTENSION) ?: 'FILE') }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="text-sm font-medium text-gray-900 truncate">{{ $file->original_name }}</div>
                                            <div class="text-xs text-gray-500">{{ number_format(($file->size ?? 0) / 1024, 1) }} Ko</div>
                                            <a class="inline-flex mt-1 text-xs text-[#1B3C35] font-medium hover:underline" href="{{ route('admin.stage-requests.attachments.download', [$stageRequest, $file->id]) }}" target="_blank">
                                                Ouvrir / Telecharger
                                            </a>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        <div x-show="showSendConfirmModal" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50" @click="showSendConfirmModal = false"></div>
            <div class="relative w-full max-w-md bg-white rounded-xl shadow-2xl border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Confirmation</h3>
                </div>
                <div class="p-4">
                    <p class="text-sm text-gray-700">Voulez-vous vraiment envoyer cet email au candidat ?</p>
                </div>
                <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-end gap-2">
                    <button type="button" @click="showSendConfirmModal = false" class="px-3 py-2 rounded-lg border border-gray-300 text-sm text-gray-700">Annuler</button>
                    <button type="button" @click="document.getElementById('send-mail-form-show').submit()" class="px-3 py-2 rounded-lg text-white text-sm" style="background: linear-gradient(135deg, #1B3C35, #2D5A4E);">Confirmer</button>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>

