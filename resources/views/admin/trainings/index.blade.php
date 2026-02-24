<x-layouts.admin>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Formations</h1>
            <p class="text-sm text-gray-500 mt-1">Gérez le catalogue de formations de l'entreprise.</p>
        </div>
        <a href="{{ route('admin.trainings.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white rounded-xl transition-all" style="background: linear-gradient(135deg, #1B3C35, #5C6E68);">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nouvelle formation
        </a>
    </div>

    @if(session('success'))
        <div class="border-l-4 p-4 rounded-r-lg" style="background: rgba(27, 60, 53, 0.1); border-color: #1B3C35;">
            <p class="text-sm font-medium" style="color: #1B3C35;">{{ session('success') }}</p>
        </div>
    @endif

    @if($trainings->count())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($trainings as $training)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow flex flex-col">
                    <div class="flex items-start justify-between mb-3">
                        <span class="text-xs px-2 py-1 rounded-full font-medium" style="background: rgba(27,60,53,0.1); color: #1B3C35;">
                            {{ $training->category_label }}
                        </span>
                        <span class="text-xs px-2 py-1 rounded-full font-medium {{ $training->status === 'published' ? 'bg-emerald-50 text-emerald-700' : ($training->status === 'archived' ? 'bg-gray-100 text-gray-500' : 'bg-yellow-50 text-yellow-700') }}">
                            {{ $training->status_label }}
                        </span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1">{{ $training->title }}</h3>
                    <p class="text-sm text-gray-500 mb-3 flex-1 line-clamp-2">{{ $training->description }}</p>
                    <div class="flex items-center justify-between text-xs text-gray-400 mb-3">
                        <span>{{ $training->enrolled_count }} inscrits</span>
                        @if($training->duration_hours)<span>{{ $training->duration_hours }}h</span>@endif
                        @if($training->start_date)<span>{{ $training->start_date->format('d/m/Y') }}</span>@endif
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.trainings.show', $training) }}" class="flex-1 text-center text-xs font-medium py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50 transition-colors">Détails</a>
                        <a href="{{ route('admin.trainings.edit', $training) }}" class="flex-1 text-center text-xs font-medium py-2 rounded-lg text-white transition-colors" style="background: #1B3C35;">Modifier</a>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $trainings->links() }}</div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            <p class="text-gray-500 mb-4">Aucune formation créée.</p>
            <a href="{{ route('admin.trainings.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white rounded-xl" style="background: #1B3C35;">Créer la première</a>
        </div>
    @endif
</div>
</x-layouts.admin>
