<x-layouts.employee>
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Catalogue de Formations</h1>
            <p class="text-sm text-gray-500 mt-1">Inscrivez-vous aux formations disponibles et suivez votre progression.</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 animate-fade-in-up">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #1B3C35, #5C6E68);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $enrolledCount }}</p>
                    <p class="text-xs text-gray-500">En cours</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #C8A96E, #d4b87a);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $completedCount }}</p>
                    <p class="text-xs text-gray-500">Terminées</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #5C6E68, #8590AA);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($totalHours, 1) }}h</p>
                    <p class="text-xs text-gray-500">Heures de formation</p>
                </div>
            </div>
        </div>
    </div>

    {{-- My Enrollments --}}
    @if($myTrainings->count())
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden animate-fade-in-up">
        <div class="px-6 py-4 border-b border-gray-100" style="background: linear-gradient(90deg, rgba(27,60,53,0.1), rgba(133,144,170,0.08));">
            <h2 class="font-semibold text-gray-900">Mes Formations</h2>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($myTrainings as $participation)
                <div class="p-5 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center
                            {{ $participation->status === 'completed' ? 'bg-emerald-100' : 'bg-blue-100' }}">
                            @if($participation->status === 'completed')
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @else
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            @endif
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $participation->training->title }}</p>
                            <div class="flex items-center gap-3 text-xs text-gray-500 mt-1">
                                @if($participation->training->start_date)
                                    <span>{{ $participation->training->start_date->format('d/m/Y') }}</span>
                                @endif
                                @if($participation->training->duration_hours)
                                    <span>{{ $participation->training->duration_hours }}h</span>
                                @endif
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $participation->status === 'completed' ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700' }}">
                                    {{ $participation->status_label }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @if($participation->status === 'enrolled')
                        <form action="{{ route('employee.trainings.unenroll', $participation->training_id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Se désinscrire</button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Available Trainings Catalog --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden animate-fade-in-up">
        <div class="px-6 py-4 border-b border-gray-100" style="background: linear-gradient(90deg, rgba(200,169,110,0.1), rgba(231,227,212,0.08));">
            <h2 class="font-semibold text-gray-900">Formations Disponibles</h2>
        </div>

        @if($availableTrainings->count())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-6">
                @foreach($availableTrainings as $training)
                    <div class="border border-gray-200 rounded-xl p-5 hover:shadow-md transition-shadow flex flex-col">
                        <div class="flex items-start justify-between mb-3">
                            <span class="text-xs px-2 py-1 rounded-full font-medium" style="background: rgba(27,60,53,0.1); color: #1B3C35;">
                                {{ $training->category_label }}
                            </span>
                            <span class="text-xs px-2 py-1 rounded-full font-medium bg-gray-100 text-gray-600">
                                {{ $training->type_label }}
                            </span>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-1">{{ $training->title }}</h3>
                        <p class="text-sm text-gray-500 mb-3 flex-1 line-clamp-2">{{ $training->description }}</p>
                        <div class="flex items-center gap-3 text-xs text-gray-400 mb-4">
                            @if($training->duration_hours)
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $training->duration_hours }}h
                                </span>
                            @endif
                            @if($training->instructor)
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    {{ $training->instructor }}
                                </span>
                            @endif
                            @if($training->start_date)
                                <span>{{ $training->start_date->format('d/m/Y') }}</span>
                            @endif
                        </div>
                        @if(in_array($training->id, $enrolledIds))
                            <span class="text-center text-xs font-medium text-emerald-600 py-2 rounded-lg bg-emerald-50 border border-emerald-200">
                                ✓ Inscrit
                            </span>
                        @elseif($training->is_full)
                            <span class="text-center text-xs font-medium text-gray-400 py-2 rounded-lg bg-gray-50 border border-gray-200">
                                Complet
                            </span>
                        @else
                            <form action="{{ route('employee.trainings.enroll', $training) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-center text-xs font-medium text-white py-2 rounded-lg transition-colors" style="background: #1B3C35;">
                                    S'inscrire
                                    @if($training->spots_left !== null)
                                        ({{ $training->spots_left }} places)
                                    @endif
                                </button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-12 text-center">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <p class="text-gray-500">Aucune formation disponible pour le moment.</p>
            </div>
        @endif
    </div>
</div>
</x-layouts.employee>
