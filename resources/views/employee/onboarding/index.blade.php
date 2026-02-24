<x-layouts.employee>
<div class="max-w-4xl mx-auto px-4 py-8">
    {{-- Welcome Header --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-8 animate-fade-in-up">
        <div class="px-8 py-6" style="background: linear-gradient(135deg, #1B3C35, #5C6E68);">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <div class="text-white">
                    <h1 class="text-2xl font-bold">Bienvenue, {{ $user->name }} !</h1>
                    <p class="text-white/80 mt-1">Suivez ces étapes pour compléter votre intégration dans l'entreprise.</p>
                </div>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="px-8 py-4 bg-gray-50 border-t border-gray-100">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-semibold text-gray-700">Progression</span>
                <span class="text-sm font-bold" style="color: #1B3C35;">{{ $progress }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="h-3 rounded-full transition-all duration-500" style="width: {{ $progress }}%; background: linear-gradient(90deg, #1B3C35, #C8A96E);"></div>
            </div>
        </div>
    </div>

    {{-- Checklist Steps --}}
    <div class="space-y-4">
        @foreach($steps as $index => $step)
            <div class="bg-white rounded-2xl shadow-sm border overflow-hidden animate-fade-in-up
                {{ $step['completed'] ? 'border-emerald-200' : 'border-gray-200' }}"
                style="animation-delay: {{ ($index + 1) * 100 }}ms;">

                <div class="flex items-start gap-4 p-6">
                    {{-- Status Icon --}}
                    <div class="flex-shrink-0 mt-0.5">
                        @if($step['completed'])
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        @else
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, rgba(27,60,53,0.1), rgba(133,144,170,0.1));">
                                @if($step['icon'] === 'camera')
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                @elseif($step['icon'] === 'user')
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                @elseif($step['icon'] === 'document')
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                @elseif($step['icon'] === 'clock')
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @elseif($step['icon'] === 'calendar')
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                @elseif($step['icon'] === 'people')
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Step Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <h3 class="font-semibold {{ $step['completed'] ? 'line-through text-gray-400' : 'text-gray-900' }}">
                                {{ $step['title'] }}
                            </h3>
                            @if($step['required'])
                                <span class="text-xs px-1.5 py-0.5 rounded-full font-medium bg-red-50 text-red-600">Requis</span>
                            @else
                                <span class="text-xs px-1.5 py-0.5 rounded-full font-medium bg-gray-100 text-gray-500">Optionnel</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500 mt-1">{{ $step['description'] }}</p>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex-shrink-0 flex items-center gap-2">
                        @if(!$step['completed'])
                            <a href="{{ $step['action_url'] }}" class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50 transition-colors">
                                {{ $step['action_label'] }}
                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>

                            @if(!in_array($step['key'], ['profile_photo', 'personal_info']))
                                <form action="{{ route('employee.onboarding.complete-step', $step['key']) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg text-white transition-colors" style="background: #1B3C35;">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Fait
                                    </button>
                                </form>
                            @endif
                        @else
                            <span class="text-xs font-medium text-emerald-600 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Complété
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Skip / Complete --}}
    <div class="mt-8 text-center">
        @if($progress === 100)
            <form action="{{ route('employee.onboarding.complete-step', 'finalize') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center px-6 py-3 text-white font-semibold rounded-xl transition-all shadow-lg hover:shadow-xl" style="background: linear-gradient(135deg, #1B3C35, #C8A96E);">
                    Terminer mon intégration
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </button>
            </form>
        @else
            <a href="{{ route('employee.dashboard') }}" class="text-sm text-gray-400 hover:text-gray-600 transition-colors">
                Passer et compléter plus tard →
            </a>
        @endif
    </div>
</div>
</x-layouts.employee>
