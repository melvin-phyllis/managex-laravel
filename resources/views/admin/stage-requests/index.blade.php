<x-dynamic-component :component="auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.employee'">
    <div class="p-6"
         x-data="{
             showTemplateModal: false,
             showSendConfirmModal: false,
             pendingSendFormId: null,
             ccEmail: @js($mailSettings['cc_email'] ?? ''),
             retainedSubject: @js($mailSettings['retained_mail_subject'] ?? 'Candidature retenue - {name}'),
             retainedBody: @js($mailSettings['retained_mail_body'] ?? 'Bonjour {name},\\n\\nVotre candidature a ete retenue.\\n\\nCordialement,\\nEquipe RH'),
             askSendConfirmation(formId) {
                this.pendingSendFormId = formId;
                this.showSendConfirmModal = true;
             },
             confirmSendMail() {
                if (this.pendingSendFormId) {
                    const form = document.getElementById(this.pendingSendFormId);
                    if (form) {
                        form.submit();
                    } else {
                        alert('Formulaire introuvable: ' + this.pendingSendFormId);
                    }
                }
                this.showSendConfirmModal = false;
                this.pendingSendFormId = null;
             }
         }">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-xl font-bold text-gray-900">Demandes de stage</h1>
        </div>

        <div class="mb-4 bg-white border border-gray-100 rounded-xl shadow-sm p-4">
            <form method="GET" action="{{ route('admin.stage-requests.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Decision finale</label>
                    <select name="final_status" class="w-full border border-gray-200 rounded-lg text-sm px-3 py-2 bg-white">
                        <option value="">Toutes</option>
                        <option value="retained" @selected(($finalStatus ?? '')==='retained')>Retenu</option>
                        <option value="waitlist" @selected(($finalStatus ?? '')==='waitlist')>Liste d'attente</option>
                        <option value="rejected" @selected(($finalStatus ?? '')==='rejected')>Rejete</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-3 py-2 rounded-lg text-white text-sm" style="background: linear-gradient(135deg, #1B3C35, #2D5A4E);">
                        Filtrer
                    </button>
                    <a href="{{ route('admin.stage-requests.index') }}" class="px-3 py-2 rounded-lg border border-gray-300 text-sm text-gray-700">
                        Reinitialiser
                    </a>
                </div>
            </form>
        </div>

        <div class="mb-4 bg-white border border-gray-100 rounded-xl shadow-sm p-4">
            <h2 class="text-sm font-semibold text-gray-900 mb-3">Parametres email (Demandes de stage)</h2>
            <div class="flex flex-wrap items-center gap-3">
                <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700">CC: {{ $mailSettings['cc_email'] ?: 'Non defini' }}</span>
                <span class="text-xs px-2 py-1 rounded-full bg-[#F0F5F3] text-[#1B3C35]">Expediteur: {{ $mailSettings['sender_email'] ?: 'RECRUITMENT_IMAP_USERNAME non defini' }}</span>
                <button type="button"
                        @click="showTemplateModal = true"
                        class="px-3 py-2 rounded-lg border border-gray-300 text-sm text-gray-700 hover:bg-gray-50">
                    Editer mail
                </button>
            </div>
            <p class="text-xs text-gray-500 mt-2">Variables disponibles: <code>{name}</code> (nom du candidat).</p>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700">Candidat</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700">Contact</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700">Profil</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700">Message</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php
                            $statusLabels = [
                                'pending' => 'En attente',
                                'reviewed' => 'Vu',
                                'shortlisted' => 'Preselectionne',
                                'rejected' => 'Rejete',
                            ];
                            $cc = $mailSettings['cc_email'] ?? config('recruitment.cc_email');
                        @endphp
                        @forelse($requests as $req)
                            <tr class="hover:bg-gray-50 cursor-pointer" ondblclick="window.location='{{ route('admin.stage-requests.show', $req) }}'">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $req->full_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $req->created_at?->format('d/m/Y H:i') }}</div>
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    <div class="break-all">{{ $req->email }}</div>
                                    <div class="text-xs text-gray-500">{{ $req->phone ?: '-' }}</div>
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    <div><strong>Ecole:</strong> {{ $req->school ?: '-' }}</div>
                                    <div><strong>Niveau:</strong> {{ $req->level ?: '-' }}</div>
                                    <div><strong>Poste:</strong> {{ $req->desired_role ?: '-' }}</div>
                                </td>
                                <td class="px-4 py-3 text-gray-700 max-w-xs">
                                    <div class="line-clamp-3 break-words" title="{{ $req->message }}">
                                        {{ $req->message ? \Illuminate\Support\Str::limit($req->message, 220) : '-' }}
                                    </div>
                                    @if($req->attachments_count > 0)
                                        <div class="mt-1 text-xs text-[#1B3C35] font-medium">{{ $req->attachments_count }} piece(s) jointe(s)</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 align-top">
                                    <form method="POST" action="{{ route('admin.stage-requests.update-status', $req) }}" class="flex flex-col gap-2">
                                        @csrf
                                        <select name="final_status"
                                                onchange="this.form.submit()"
                                                class="border border-gray-200 rounded-lg text-sm px-2 py-2 bg-white">
                                            <option value="">A decider</option>
                                            <option value="retained" @selected($req->final_status==='retained')>Retenu</option>
                                            <option value="waitlist" @selected($req->final_status==='waitlist')>Liste d'attente</option>
                                            <option value="rejected" @selected($req->final_status==='rejected')>Rejete</option>
                                        </select>
                                    </form>

                                    @if($req->final_status === 'retained')
                                        <div class="mt-2 flex items-center gap-2">
                                            <form id="send-mail-form-{{ $req->id }}" method="POST" action="{{ route('admin.stage-requests.send-retained-mail', $req) }}">
                                                @csrf
                                                <button type="button"
                                                        @click="askSendConfirmation('send-mail-form-{{ $req->id }}')"
                                                        class="inline-flex items-center justify-center px-3 py-2 rounded-lg border border-emerald-300 bg-emerald-50 text-emerald-700 text-sm">
                                                    {{ $req->retained_mail_sent_at ? 'Renvoyer mail' : 'Envoyer un mail' }}
                                                </button>
                                            </form>
                                            @if($req->retained_mail_sent_at)
                                                <span class="text-xs px-2 py-1 rounded-full bg-emerald-100 text-emerald-800">
                                                    Envoye {{ $req->retained_mail_sent_at->format('d/m H:i') }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    Aucune demande de stage pour le moment.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-gray-100">
                {{ $requests->links() }}
            </div>
        </div>

        <div x-show="showTemplateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50" @click="showTemplateModal = false"></div>
            <div class="relative w-full max-w-2xl bg-white rounded-xl shadow-2xl border border-gray-200">
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Edition template mail (candidature retenue)</h3>
                    <button type="button" @click="showTemplateModal = false" class="text-gray-500 hover:text-gray-700 text-xl leading-none">&times;</button>
                </div>
                <form method="POST" action="{{ route('admin.stage-requests.settings.mail') }}" class="p-4 space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Email expediteur (fixe)</label>
                        <input type="text" value="{{ $mailSettings['sender_email'] ?: 'RECRUITMENT_IMAP_USERNAME non defini' }}" readonly class="w-full border border-gray-200 rounded-lg text-sm px-3 py-2 bg-gray-50 text-gray-600">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">CC</label>
                        <input type="email" name="cc_email" x-model="ccEmail" class="w-full border border-gray-200 rounded-lg text-sm px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Objet</label>
                        <input type="text" name="retained_mail_subject" x-model="retainedSubject" class="w-full border border-gray-200 rounded-lg text-sm px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Contenu</label>
                        <textarea name="retained_mail_body" rows="10" x-model="retainedBody" class="w-full border border-gray-200 rounded-lg text-sm px-3 py-2"></textarea>
                    </div>
                    <div class="flex items-center justify-end gap-2">
                        <button type="button" @click="showTemplateModal = false" class="px-3 py-2 rounded-lg border border-gray-300 text-sm text-gray-700">Annuler</button>
                        <button type="submit" class="px-3 py-2 rounded-lg text-white text-sm" style="background: linear-gradient(135deg, #1B3C35, #2D5A4E);">
                            Sauvegarder
                        </button>
                    </div>
                </form>
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
                    <button type="button" @click="confirmSendMail()" class="px-3 py-2 rounded-lg text-white text-sm" style="background: linear-gradient(135deg, #1B3C35, #2D5A4E);">Confirmer</button>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>

