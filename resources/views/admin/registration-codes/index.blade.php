<x-layouts.admin>
    <div class="p-6">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-extrabold text-[#1B3C35]">Codes d'Inscription</h1>
                <p class="mt-2 text-gray-600">Gérez les codes de sécurité pour la création autonome des comptes.</p>
            </div>
            <div class="bg-gradient-to-br from-[#1B3C35] to-[#3D7A6A] p-4 rounded-2xl shadow-lg border border-white/20">
                <div class="text-white/80 text-xs font-medium uppercase tracking-wider">Total actifs</div>
                <div class="text-2xl font-bold text-white">{{ $codes->where('status', 'active')->count() }}</div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 flex items-center p-4 bg-green-50 rounded-2xl border border-green-100 animate-fade-in-down">
                <div
                    class="flex-shrink-0 w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white mr-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="text-green-800 font-medium">{{ session('success') }}</div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Formulaire de génération -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden sticky top-24">
                    <div class="p-6 border-b border-gray-50 bg-gray-50/50">
                        <h2 class="text-xl font-bold text-[#1B3C35]">Nouveau Code</h2>
                        <p class="text-sm text-gray-500">Générez un code sécurisé pour un futur utilisateur.</p>
                    </div>
                    <form action="{{ route('admin.registration-codes.store') }}" method="POST" class="p-6 space-y-5">
                        @csrf
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Code Unique</label>
                            <div class="flex gap-2">
                                <input type="text" name="code" value="{{ strtoupper(Str::random(10)) }}" readonly
                                    class="flex-1 bg-gray-50 border-gray-200 rounded-xl text-center font-mono font-bold text-lg text-[#1B3C35] focus:ring-0">
                                <button type="button" onclick="location.reload()"
                                    class="p-3 bg-gray-100 text-gray-500 rounded-xl hover:bg-gray-200 transition-colors"
                                    title="Régénérer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Rôle Attribué</label>
                            <select name="role"
                                class="w-full border-gray-200 rounded-xl focus:border-[#1B3C35] focus:ring-[#1B3C35]">
                                <option value="employee">👨‍💼 Employé</option>
                                <option value="admin">🔒 Administrateur</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Département</label>
                                <select name="department_id"
                                    class="w-full border-gray-200 rounded-xl focus:border-[#1B3C35] focus:ring-[#1B3C35]">
                                    <option value="">Tous les départements</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Poste</label>
                                <select name="position_id"
                                    class="w-full border-gray-200 rounded-xl focus:border-[#1B3C35] focus:ring-[#1B3C35]">
                                    <option value="">Tous les postes</option>
                                    @foreach($positions as $pos)
                                        <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Email du destinataire
                                (Optionnel)</label>
                            <input type="email" name="email" placeholder="user@example.com"
                                class="w-full border-gray-200 rounded-xl focus:border-[#1B3C35] focus:ring-[#1B3C35]">
                            <p class="mt-1 text-xs text-gray-400">Si spécifié, seul cet email pourra utiliser le code.
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Date d'expiration</label>
                            <input type="datetime-local" name="expires_at"
                                class="w-full border-gray-200 rounded-xl focus:border-[#1B3C35] focus:ring-[#1B3C35]">
                        </div>

                        <button type="submit"
                            class="w-full bg-[#1B3C35] text-white py-4 rounded-2xl font-bold shadow-lg shadow-[#1B3C35]/20 hover:bg-[#3D7A6A] active:scale-95 transition-all">
                            Générer et Enregistrer
                        </button>
                    </form>
                </div>
            </div>

            <!-- Liste des codes -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-white">
                        <h2 class="text-xl font-bold text-[#1B3C35]">Historique des Codes</h2>
                        <span class="text-xs text-gray-400 font-medium">{{ $codes->total() }} codes au total</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Code
                                        / Rôle</th>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Affiliation</th>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Statut</th>
                                    <th
                                        class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($codes as $code)
                                    <tr class="group hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-5">
                                            <div class="flex items-center">
                                                <div
                                                    class="w-10 h-10 rounded-lg bg-[#1B3C35]/10 flex items-center justify-center text-[#1B3C35] font-bold text-sm mr-3">
                                                    {{ substr($code->code, 0, 2) }}
                                                </div>
                                                <div>
                                                    <div class="font-mono font-extrabold text-[#1B3C35] text-base copy-code cursor-pointer transition-transform active:scale-90"
                                                        onclick="navigator.clipboard.writeText('{{ $code->code }}'); alert('Code copié !')"
                                                        title="Cliquez pour copier">
                                                        {{ $code->code }}
                                                    </div>
                                                    <div
                                                        class="text-[10px] font-bold uppercase tracking-tight {{ $code->role === 'admin' ? 'text-red-500' : 'text-blue-500' }}">
                                                        {{ $code->role }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            @if($code->department || $code->position)
                                                <div class="text-sm font-semibold text-gray-700">
                                                    {{ $code->department->name ?? 'Tous Depts' }}</div>
                                                <div class="text-xs text-gray-400">{{ $code->position->name ?? 'Tous Postes' }}
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-400 italic">Libre accès</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-5">
                                            @if($code->status === 'active')
                                                <div
                                                    class="flex items-center text-green-600 font-bold text-xs uppercase bg-green-50 px-3 py-1.5 rounded-full w-max border border-green-100">
                                                    <span
                                                        class="w-1.5 h-1.5 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                                                    Actif
                                                </div>
                                            @elseif($code->status === 'used')
                                                <div class="text-gray-400 text-xs font-medium">
                                                    Utilisé par <span
                                                        class="text-gray-700 font-bold underline">{{ $code->user->name ?? 'Inconnu' }}</span>
                                                    <div class="text-[10px] mt-0.5">{{ $code->used_at->format('d/m/Y H:i') }}
                                                    </div>
                                                </div>
                                            @else
                                                <div
                                                    class="text-red-400 text-xs font-bold uppercase bg-red-50 px-3 py-1.5 rounded-full w-max border border-red-100">
                                                    Expiré</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-5 text-right">
                                            <form action="{{ route('admin.registration-codes.destroy', $code) }}"
                                                method="POST"
                                                onsubmit="return confirm('Attention ! Supprimer ce code empêchera définitivement son utilisation.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-6 bg-white border-t border-gray-50">
                        {{ $codes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes fade-in-down {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-down {
            animation: fade-in-down 0.4s ease-out;
        }
    </style>
</x-layouts.admin>
