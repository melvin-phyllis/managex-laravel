<x-layouts.admin>
    <div class="space-y-6">
        <!-- Breadcrumb -->
        <nav class="flex animate-fade-in-up" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-[#1B3C35]">
                        <x-icon name="home" class="w-4 h-4 mr-2" />
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-icon name="chevron-right" class="w-6 h-6 text-gray-400" />
                        <a href="{{ route('admin.surveys.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-[#1B3C35] md:ml-2">Sondages</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-icon name="chevron-right" class="w-6 h-6 text-gray-400" />
                        <a href="{{ route('admin.surveys.show', $survey) }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-[#1B3C35] md:ml-2">{{ Str::limit($survey->titre, 20) }}</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <x-icon name="chevron-right" class="w-6 h-6 text-gray-400" />
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Participants</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="flex items-center justify-between animate-fade-in-up animation-delay-100">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Suivi des participants</h1>
                <p class="text-gray-500 mt-1 flex items-center gap-2">
                    <x-icon name="users" class="w-4 h-4" />
                    {{ $survey->titre }}
                </p>
            </div>
            <a href="{{ route('admin.surveys.show', $survey) }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition-colors">
                <x-icon name="arrow-left" class="w-5 h-5 mr-2" />
                Retour
            </a>
        </div>

        @if($survey->is_anonymous)
            <div class="bg-purple-50 border border-purple-200 rounded-xl p-4 flex items-start gap-3">
                <x-icon name="shield" class="w-5 h-5 text-purple-600 mt-0.5" />
                <div>
                    <h3 class="text-sm font-medium text-purple-900">Ce sondage est anonyme</h3>
                    <p class="mt-1 text-sm text-purple-700">Vous pouvez voir qui a participé afin de faire des relances, mais <span class="font-semibold">leurs réponses individuelles restent secrètes</span> et ne peuvent pas être consultées sur la page des résultats.</p>
                </div>
            </div>
        @endif

        <!-- Lists -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 animate-fade-in-up animation-delay-200">

            <!-- Respondents Column -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-green-50/30">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <x-icon name="check-circle" class="w-5 h-5 text-green-500" />
                        Ont répondu
                    </h2>
                    <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                        {{ $respondents->count() }} employés
                    </span>
                </div>
                <div class="p-0">
                    @if($respondents->isNotEmpty())
                        <ul class="divide-y divide-gray-100 h-[600px] overflow-y-auto custom-scrollbar">
                            @foreach($respondents as $user)
                                <li class="p-4 hover:bg-gray-50 transition-colors flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        @if($user->avatar && avatar_url($user->avatar))
                                            <img src="{{ avatar_url($user->avatar) }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover">
                                        @else
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold" style="background-color: rgba(45, 90, 78, 0.15); color: #2D5A4E;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $user->departmentPosition }}</p>
                                        </div>
                                    </div>
                                    <span class="text-green-500">
                                        <x-icon name="check" class="w-5 h-5" />
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="p-8 text-center">
                            <x-icon name="users" class="w-10 h-10 text-gray-300 mx-auto mb-3" />
                            <p class="text-sm text-gray-500">Personne n'a encore répondu à ce sondage.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Non-Respondents Column -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-orange-50/30">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <x-icon name="clock" class="w-5 h-5 text-orange-500" />
                        En attente
                    </h2>
                    <span class="bg-orange-100 text-orange-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                        {{ $nonRespondents->count() }} employés
                    </span>
                </div>
                <div class="p-0">
                    @if($nonRespondents->isNotEmpty())
                        <ul class="divide-y divide-gray-100 h-[600px] overflow-y-auto custom-scrollbar">
                            @foreach($nonRespondents as $user)
                                <li class="p-4 hover:bg-gray-50 transition-colors flex items-center justify-between group">
                                    <div class="flex items-center gap-3">
                                        @if($user->avatar && avatar_url($user->avatar))
                                            <img src="{{ avatar_url($user->avatar) }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover opacity-60">
                                        @else
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold opacity-60" style="background-color: rgba(245, 158, 11, 0.15); color: #B45309;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 group-hover:text-orange-700 transition-colors">{{ $user->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $user->departmentPosition }}</p>
                                        </div>
                                    </div>
                                    <!-- In the future, a "Relancer" button could go here -->
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="p-8 text-center items-center justify-center flex flex-col h-full">
                            <x-icon name="party-popper" class="w-12 h-12 text-green-400 mx-auto mb-3" />
                            <p class="text-sm font-medium text-green-700">Tout le monde a répondu !</p>
                            <p class="text-xs text-gray-500 mt-1">100% de taux de participation atteint.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-layouts.admin>
