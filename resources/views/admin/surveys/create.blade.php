<x-layouts.admin>
    <div class="space-y-6">
        <!-- Breadcrumb -->
        <nav class="flex animate-fade-in-up" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('admin.surveys.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">Sondages</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Nouveau</span>
                    </div>
                </li>
            </ol>
        </nav>
        <!-- Header -->
        <div class="flex items-center justify-between animate-fade-in-up animation-delay-100">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Créer un sondage</h1>
                <p class="text-gray-500 mt-1">Ajoutez des questions pour votre sondage interne</p>
            </div>
            <a href="{{ route('admin.surveys.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                <x-icon name="arrow-left" class="w-5 h-5 mr-2" />
                Retour
            </a>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.surveys.store') }}" method="POST" x-data="surveyForm()" class="space-y-6">
            @csrf

            <!-- Survey Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 animate-fade-in-up animation-delay-200">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <x-icon name="info" class="w-5 h-5 text-blue-500" />
                    Informations générales
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="titre" class="block text-sm font-medium text-gray-700 mb-1">Titre du sondage *</label>
                        <input type="text" name="titre" id="titre" value="{{ old('titre') }}" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('titre') border-red-500 @enderror" placeholder="Ex: Sondage de satisfaction Q1">
                        @error('titre')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" id="description" rows="3" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-500 @enderror" placeholder="Décrivez l'objectif de ce sondage...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="date_limite" class="block text-sm font-medium text-gray-700 mb-1">Date limite</label>
                        <div class="relative">
                            <input type="date" name="date_limite" id="date_limite" value="{{ old('date_limite') }}" class="w-full pl-10 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('date_limite') border-red-500 @enderror">
                            <x-icon name="calendar" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                        </div>
                        @error('date_limite')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center h-full pt-6">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-5 h-5">
                            <span class="ml-2 text-sm text-gray-700">Activer immédiatement</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Questions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 animate-fade-in-up animation-delay-300">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <x-icon name="list-checks" class="w-5 h-5 text-blue-500" />
                        Questions
                    </h2>
                    <button type="button" @click="addQuestion()" class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-700 text-sm font-medium rounded-lg hover:bg-blue-100 transition-colors border border-blue-200">
                        <x-icon name="plus" class="w-4 h-4 mr-2" />
                        Ajouter une question
                    </button>
                </div>

                <div class="space-y-4">
                    <template x-for="(question, index) in questions" :key="index">
                        <div class="border border-gray-200 rounded-xl p-5 relative bg-gray-50/50 hover:bg-white hover:shadow-sm transition-all duration-200">
                            <button type="button" @click="removeQuestion(index)" class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition-colors p-1 hover:bg-red-50 rounded">
                                <x-icon name="trash-2" class="w-5 h-5" />
                            </button>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pr-8">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Question <span x-text="index + 1"></span> *</label>
                                    <input type="text" :name="'questions[' + index + '][question]'" x-model="question.question" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Entrez votre question ici...">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Type de réponse *</label>
                                    <div class="relative">
                                        <select :name="'questions[' + index + '][type]'" x-model="question.type" @change="updateQuestionType(index)" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 appearance-none">
                                            <option value="text">Texte libre</option>
                                            <option value="choice">Choix multiples</option>
                                            <option value="rating">Note (1-5)</option>
                                            <option value="yesno">Oui / Non</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="flex items-center h-full pt-6">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" :name="'questions[' + index + '][is_required]'" :id="'required_' + index" value="1" x-model="question.is_required" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700">Réponse obligatoire</span>
                                    </label>
                                </div>

                                <!-- Options for choice type -->
                                <div x-show="question.type === 'choice'" class="md:col-span-2 pl-4 border-l-2 border-blue-200 mt-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Options (une par ligne)</label>
                                    <textarea :name="'questions[' + index + '][options]'" x-model="question.options" rows="3" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 bg-white" placeholder="Trés satisfait&#10;Satisfait&#10;Neutre&#10;Insatisfait"></textarea>
                                    <p class="text-xs text-gray-500 mt-1">Chaque ligne correspondra à une option de réponse.</p>
                                </div>
                            </div>

                            <input type="hidden" :name="'questions[' + index + '][ordre]'" :value="index + 1">
                        </div>
                    </template>

                    <div x-show="questions.length === 0" class="text-center py-12 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50">
                        <div class="mx-auto w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                            <x-icon name="help-circle" class="w-6 h-6 text-gray-400" />
                        </div>
                        <p class="text-gray-500 font-medium">Aucune question ajoutée pour le moment.</p>
                        <p class="text-gray-400 text-sm mt-1">Commencez par ajouter des questions à votre sondage.</p>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.surveys.index') }}" class="px-4 py-2.5 text-gray-700 hover:text-gray-900 font-medium">Annuler</a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition-colors shadow-lg shadow-blue-500/25 flex items-center">
                    <x-icon name="check" class="w-5 h-5 mr-2" />
                    Créer le sondage
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script nonce="{{ $cspNonce ?? '' }}">
        function surveyForm() {
            return {
                questions: [],
                addQuestion() {
                    this.questions.push({
                        question: '',
                        type: 'text',
                        is_required: true,
                        options: ''
                    });
                },
                removeQuestion(index) {
                    this.questions.splice(index, 1);
                },
                updateQuestionType(index) {
                    if (this.questions[index].type !== 'choice') {
                        this.questions[index].options = '';
                    }
                }
            }
        }
    </script>
    @endpush
</x-layouts.admin>
