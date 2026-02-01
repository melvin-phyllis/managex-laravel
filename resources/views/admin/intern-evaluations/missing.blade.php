<x-layouts.admin>
    <div class="space-y-6">
        <!-- Header -->
        <x-table-header title="Évaluations Manquantes" subtitle="{{ $lastWeekStart->format('d/m/Y') }} - Semaine écoulée">
            <x-slot:icon>
                <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/20">
                    <x-icon name="alert-triangle" class="w-6 h-6 text-white" />
                </div>
            </x-slot:icon>
            <x-slot:actions>
                <a href="{{ route('admin.intern-evaluations.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition-all text-sm">
                    <x-icon name="arrow-left" class="w-4 h-4 mr-2" />
                    Retour
                </a>
            </x-slot:actions>
        </x-table-header>

        @if($internsWithMissing->isEmpty())
            <!-- All good! -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-icon name="check-circle" class="w-8 h-8 text-green-600" />
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Toutes les évaluations sont complètes ! 🎉</h3>
                <p class="text-gray-500">Aucune évaluation manquante pour la semaine du {{ $lastWeekStart->format('d/m/Y') }}</p>
            </div>
        @else
            <!-- Missing by tutor -->
            <div class="space-y-6">
                @foreach($missingByTutor as $supervisorId => $interns)
                    @php $tutor = $interns->first()->supervisor; @endphp
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-4 bg-amber-50 border-b border-amber-100 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-amber-500 rounded-full flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr($tutor->name ?? 'T', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $tutor->name ?? 'Tuteur inconnu' }}</p>
                                    <p class="text-sm text-amber-700">{{ $interns->count() }} évaluation(s) manquante(s)</p>
                                </div>
                            </div>
                            <a href="mailto:{{ $tutor->email ?? '' }}" class="inline-flex items-center px-3 py-1.5 bg-amber-600 text-white rounded-lg text-sm font-medium hover:bg-amber-700 transition-colors">
                                <x-icon name="mail" class="w-4 h-4 mr-1" />
                                Contacter
                            </a>
                        </div>
                        <div class="p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($interns as $intern)
                                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                                        <div class="w-8 h-8 bg-violet-100 text-violet-600 rounded-full flex items-center justify-center font-semibold text-sm">
                                            {{ strtoupper(substr($intern->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900 text-sm">{{ $intern->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $intern->department->name ?? 'Sans département' }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Summary -->
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <x-icon name="info" class="w-6 h-6 text-amber-600" />
                    </div>
                    <div>
                        <h4 class="font-semibold text-amber-800 mb-1">Récapitulatif</h4>
                        <p class="text-amber-700 text-sm">
                            <strong>{{ $internsWithMissing->count() }}</strong> stagiaire(s) n'ont pas été évalué(s) pour la semaine du {{ $lastWeekStart->format('d/m/Y') }}.
                            Les tuteurs concernés ont été notifiés par email.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layouts.admin>
