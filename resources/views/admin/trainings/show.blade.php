<x-layouts.admin>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $training->title }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $training->category_label }} · {{ $training->type_label }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.trainings.edit', $training) }}" class="px-4 py-2 text-sm font-medium rounded-xl border border-gray-200 text-gray-700 hover:bg-gray-50">Modifier</a>
            <a href="{{ route('admin.trainings.index') }}" class="text-sm text-gray-500 hover:text-gray-700 py-2">← Retour</a>
        </div>
    </div>

    @if(session('success'))
        <div class="border-l-4 p-4 rounded-r-lg" style="background: rgba(27, 60, 53, 0.1); border-color: #1B3C35;">
            <p class="text-sm font-medium" style="color: #1B3C35;">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Info --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div><p class="text-xs text-gray-500">Statut</p><p class="font-semibold text-gray-900">{{ $training->status_label }}</p></div>
                <div><p class="text-xs text-gray-500">Durée</p><p class="font-semibold text-gray-900">{{ $training->duration_hours ?? '-' }}h</p></div>
                <div><p class="text-xs text-gray-500">Formateur</p><p class="font-semibold text-gray-900">{{ $training->instructor ?? '-' }}</p></div>
                <div><p class="text-xs text-gray-500">Lieu</p><p class="font-semibold text-gray-900">{{ $training->location ?? '-' }}</p></div>
            </div>
            @if($training->description)
                <div class="pt-4 border-t"><p class="text-sm text-gray-700">{{ $training->description }}</p></div>
            @endif
            @if($training->start_date)
                <div class="pt-4 border-t flex gap-6">
                    <div><p class="text-xs text-gray-500">Début</p><p class="font-medium text-gray-900">{{ $training->start_date->format('d/m/Y') }}</p></div>
                    @if($training->end_date)<div><p class="text-xs text-gray-500">Fin</p><p class="font-medium text-gray-900">{{ $training->end_date->format('d/m/Y') }}</p></div>@endif
                </div>
            @endif
        </div>

        {{-- Stats --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-4">
            <h3 class="font-semibold text-gray-900">Participants</h3>
            <div class="text-center py-4">
                <p class="text-4xl font-bold" style="color: #1B3C35;">{{ $training->participants->where('status', '!=', 'cancelled')->count() }}</p>
                <p class="text-xs text-gray-500">inscrits{{ $training->max_participants ? ' / '.$training->max_participants : '' }}</p>
            </div>
        </div>
    </div>

    {{-- Participants List --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100"><h3 class="font-semibold text-gray-900">Liste des participants</h3></div>
        @if($training->participants->count())
            <div class="divide-y divide-gray-50">
                @foreach($training->participants as $p)
                    <div class="p-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600">
                                {{ strtoupper(substr($p->user->name ?? '?', 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 text-sm">{{ $p->user->name ?? 'Utilisateur supprimé' }}</p>
                                <p class="text-xs text-gray-500">{{ $p->user->poste ?? '' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs px-2 py-1 rounded-full font-medium
                                {{ $p->status === 'completed' ? 'bg-emerald-50 text-emerald-700' : ($p->status === 'cancelled' ? 'bg-red-50 text-red-700' : 'bg-blue-50 text-blue-700') }}">
                                {{ $p->status_label }}
                            </span>
                            @if($p->status === 'enrolled')
                                <form action="{{ route('admin.trainings.mark-completed', [$training, $p->user]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs font-medium text-white px-3 py-1 rounded-lg" style="background: #1B3C35;">✓ Terminé</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-8 text-center"><p class="text-gray-500 text-sm">Aucun participant inscrit.</p></div>
        @endif
    </div>
</div>
</x-layouts.admin>
