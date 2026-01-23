<x-layouts.admin>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Créer un sondage</h1>
                <p class="text-gray-500 mt-1">Ajoutez des questions pour votre sondage interne</p>
            </div>
            <a href="{{ route('admin.surveys.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour
            </a>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.surveys.store') }}" method="POST" x-data="surveyForm()" class="space-y-6">
            @csrf

            <!-- Survey Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations générales</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="titre" class="block text-sm font-medium text-gray-700 mb-1">Titre du sondage *</label>
                        <input type="text" name="titre" id="titre" value="{{ old('titre') }}" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('titre') border-red-500 @enderror">
                        @error('titre')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" id="description" rows="3" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="date_limite" class="block text-sm font-medium text-gray-700 mb-1">Date limite</label>
                        <input type="date" name="date_limite" id="date_limite" value="{{ old('date_limite') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('date_limite') border-red-500 @enderror">
                        @error('date_limite')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="is_active" class="ml-2 text-sm text-gray-700">Activer immédiatement</label>
                    </div>
                </div>
            </div>

            <!-- Questions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Questions</h2>
                    <button type="button" @click="addQuestion()" class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 text-sm font-medium rounded-lg hover:bg-blue-200 transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Ajouter une question
                    </button>
                </div>

                <div class="space-y-4">
                    <template x-for="(question, index) in questions" :key="index">
                        <div class="border border-gray-200 rounded-lg p-4 relative">
                            <button type="button" @click="removeQuestion(index)" class="absolute top-2 right-2 text-red-500 hover:text-red-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Question *</label>
                                    <input type="text" :name="'questions[' + index + '][question]'" x-model="question.question" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Type de réponse *</label>
                                    <select :name="'questions[' + index + '][type]'" x-model="question.type" @change="updateQuestionType(index)" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                        <option value="text">Texte libre</option>
                                        <option value="choice">Choix multiples</option>
                                        <option value="rating">Note (1-5)</option>
                                        <option value="yesno">Oui / Non</option>
                                    </select>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" :name="'questions[' + index + '][is_required]'" :id="'required_' + index" value="1" x-model="question.is_required" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <label :for="'required_' + index" class="ml-2 text-sm text-gray-700">Réponse obligatoire</label>
                                </div>

                                <!-- Options for choice type -->
                                <div x-show="question.type === 'choice'" class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Options (une par ligne)</label>
                                    <textarea :name="'questions[' + index + '][options]'" x-model="question.options" rows="3" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Option 1&#10;Option 2&#10;Option 3"></textarea>
                                </div>
                            </div>

                            <input type="hidden" :name="'questions[' + index + '][ordre]'" :value="index + 1">
                        </div>
                    </template>

                    <div x-show="questions.length === 0" class="text-center py-8 text-gray-500">
                        <p>Aucune question ajoutée. Cliquez sur "Ajouter une question" pour commencer.</p>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-end space-x-4">
                <a href="{{ route('admin.surveys.index') }}" class="px-4 py-2 text-gray-700 hover:text-gray-900">Annuler</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    Créer le sondage
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
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
