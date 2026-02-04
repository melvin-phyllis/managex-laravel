@php
    $isAdmin = auth()->user()->isAdmin();
    $routePrefix = $isAdmin ? 'admin.tutor.evaluations' : 'employee.tutor.evaluations';
@endphp

<x-dynamic-component :component="$isAdmin ? 'layouts.admin' : 'layouts.employee'">
    <div class="space-y-6" x-data="evaluationForm()">
        <!-- Header -->
        <x-table-header title="Modifier l'évaluation" subtitle="{{ $evaluation->week_label }}">
            <x-slot:icon>
                <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/20">
                    <x-icon name="edit-3" class="w-6 h-6 text-white" />
                </div>
            </x-slot:icon>
            <x-slot:actions>
                <a href="{{ route($routePrefix . '.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition-all text-sm">
                    <x-icon name="arrow-left" class="w-4 h-4 mr-2" />
                    Retour
                </a>
            </x-slot:actions>
        </x-table-header>

        <!-- Draft Warning -->
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-center gap-3">
            <x-icon name="alert-circle" class="w-5 h-5 text-amber-600" />
            <p class="text-amber-800 text-sm">
                Cette évaluation est un brouillon. Modifiez-la et soumettez-la pour qu'elle soit visible par le stagiaire.
            </p>
        </div>

        <form action="{{ route($routePrefix . '.update', $evaluation) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Criteria Cards -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @foreach($criteria as $key => $criterion)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $criterion['label'] }}</h3>
                                <p class="text-sm text-gray-500">{{ $criterion['description'] }}</p>
                            </div>
                            <div class="text-2xl font-bold text-violet-600" x-text="scores.{{ $key }}.toFixed(1) + '/2.5'"></div>
                        </div>

                        <div class="mb-4">
                            <input type="range" 
                                   name="{{ $key }}_score" 
                                   min="0" 
                                   max="2.5" 
                                   step="0.5" 
                                   x-model="scores.{{ $key }}"
                                   value="{{ old($key.'_score', $evaluation->{$key.'_score'}) }}"
                                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-violet-600">
                            <div class="flex justify-between text-xs text-gray-400 mt-1">
                                <span>0</span>
                                <span>0.5</span>
                                <span>1.0</span>
                                <span>1.5</span>
                                <span>2.0</span>
                                <span>2.5</span>
                            </div>
                        </div>

                        <textarea name="{{ $key }}_comment" 
                                  rows="3" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 resize-none"
                                  placeholder="Commentaire...">{{ old($key.'_comment', $evaluation->{$key.'_comment'}) }}</textarea>
                    </div>
                @endforeach
            </div>

            <!-- Total Score Preview -->
            <div class="bg-gradient-to-r from-violet-500 to-purple-600 rounded-2xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium opacity-90">Note totale</h3>
                        <p class="text-sm opacity-75">Somme des 4 critères</p>
                    </div>
                    <div class="text-center">
                        <div class="text-5xl font-bold" x-text="totalScore.toFixed(1)"></div>
                        <div class="text-lg opacity-75">/10</div>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold" x-text="gradeLetter"></div>
                        <div class="text-sm opacity-75" x-text="gradeLabel"></div>
                    </div>
                </div>
            </div>

            <!-- General Comments -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-4">
                <h3 class="text-lg font-semibold text-gray-900">Commentaires généraux</h3>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bilan de la semaine</label>
                    <textarea name="general_comment" 
                              rows="4" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 resize-none">{{ old('general_comment', $evaluation->general_comment) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Objectifs pour la semaine prochaine</label>
                    <textarea name="objectives_next_week" 
                              rows="3" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 resize-none">{{ old('objectives_next_week', $evaluation->objectives_next_week) }}</textarea>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <p class="text-sm text-gray-500">
                    <x-icon name="info" class="w-4 h-4 inline mr-1" />
                    Une fois soumise, l'évaluation ne pourra plus être modifiée.
                </p>
                <div class="flex gap-3">
                    <button type="submit" name="action" value="draft" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition-colors">
                        Sauvegarder brouillon
                    </button>
                    <button type="submit" name="action" value="submit" class="px-6 py-2.5 bg-violet-600 text-white rounded-xl font-medium hover:bg-violet-700 transition-colors">
                        Soumettre l'évaluation
                    </button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script nonce="{{ $cspNonce ?? '' }}">
        function evaluationForm() {
            return {
                scores: {
                    discipline: {{ old('discipline_score', $evaluation->discipline_score ?? 0) }},
                    behavior: {{ old('behavior_score', $evaluation->behavior_score ?? 0) }},
                    skills: {{ old('skills_score', $evaluation->skills_score ?? 0) }},
                    communication: {{ old('communication_score', $evaluation->communication_score ?? 0) }}
                },
                get totalScore() {
                    return parseFloat(this.scores.discipline) + parseFloat(this.scores.behavior) + parseFloat(this.scores.skills) + parseFloat(this.scores.communication);
                },
                get gradeLetter() {
                    const score = this.totalScore;
                    if (score >= 9) return 'A';
                    if (score >= 7) return 'B';
                    if (score >= 5) return 'C';
                    if (score >= 3) return 'D';
                    return 'E';
                },
                get gradeLabel() {
                    const labels = { 'A': 'Excellent', 'B': 'Bien', 'C': 'Satisfaisant', 'D': 'À améliorer', 'E': 'Insuffisant' };
                    return labels[this.gradeLetter];
                }
            }
        }
    </script>
    @endpush
</x-dynamic-component>

