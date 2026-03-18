<x-layouts.admin>
    <div class="p-6">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-xl font-bold text-gray-900">Demandes d'accès</h1>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                <p class="font-semibold">Erreur de validation :</p>
                <ul class="list-disc list-inside mt-1 text-sm">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-100">
                <p class="text-sm text-gray-600">
                    Validez ou refusez les demandes. (Les champs de démo existants sont réutilisés en “accès”.)
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700 whitespace-nowrap">Entreprise</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700 whitespace-nowrap">Contact</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700 whitespace-nowrap">Email</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700 whitespace-nowrap">Taille</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700 whitespace-nowrap">Statut</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700 whitespace-nowrap">Note</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700 whitespace-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php
                            $statusLabels = [
                                'pending' => 'En attente',
                                'needs_info' => 'Infos requises',
                                'approved' => 'Approuvée',
                                'rejected' => 'Refusée',
                            ];
                        @endphp
                        @foreach($requests as $req)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900 break-words max-w-[220px]">{{ $req->company_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $req->created_at?->diffForHumans() }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900 break-words max-w-[220px]">{{ $req->contact_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $req->phone ?: '-' }}</div>
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    <div class="break-all max-w-[260px]">{{ $req->email }}</div>
                                </td>
                                <td class="px-4 py-3 text-gray-700">{{ $req->company_size }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold whitespace-nowrap
                                        @if($req->status === 'approved') bg-green-100 text-green-800
                                        @elseif($req->status === 'rejected') bg-red-100 text-red-800
                                        @elseif($req->status === 'needs_info') bg-amber-100 text-amber-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $statusLabels[$req->status] ?? ($req->status ?: '—') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    <div class="max-w-[320px] break-words">
                                        {{ $req->admin_note ?: '-' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-top" x-data="{ status: '{{ $req->status }}' }">
                                    <form method="POST" action="{{ route('admin.access-requests.update-status', $req) }}" class="flex flex-col gap-2">
                                        @csrf
                                        <div class="flex flex-wrap items-center gap-2">
                                            <select name="status"
                                                    x-model="status"
                                                    class="border border-gray-200 rounded-lg text-sm px-2 py-2 bg-white">
                                                <option value="pending" @selected($req->status==='pending')>En attente</option>
                                                <option value="needs_info" @selected($req->status==='needs_info')>Infos requises</option>
                                                <option value="approved" @selected($req->status==='approved')>Approuvée</option>
                                                <option value="rejected" @selected($req->status==='rejected')>Refusée</option>
                                            </select>
                                            <button type="submit" class="px-3 py-2 rounded-lg text-white text-sm shrink-0" style="background: linear-gradient(135deg, #1B3C35, #2D5A4E);">
                                                Valider
                                            </button>
                                        </div>
                                        {{-- Label visible uniquement pour "Infos requises" --}}
                                        <p x-show="status === 'needs_info'" x-transition class="text-sm font-medium text-amber-800">
                                            Indiquez ce qui manque (sera envoyé par email au demandeur) :
                                        </p>
                                        <input type="text"
                                               name="admin_note"
                                               :placeholder="status === 'needs_info' ? 'Ex. : Merci de préciser la taille de l’équipe et l’usage prévu.' : 'Note (optionnel)'"
                                               :class="status === 'needs_info' ? 'border-2 border-amber-300 rounded-lg text-sm px-2 py-2 w-full bg-amber-50/50 focus:border-amber-500 focus:ring-1 focus:ring-amber-500' : 'border border-gray-200 rounded-lg text-sm px-2 py-2 w-full max-w-md'"
                                               value="{{ old('admin_note', $req->admin_note) }}">
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-gray-100">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
</x-layouts.admin>

