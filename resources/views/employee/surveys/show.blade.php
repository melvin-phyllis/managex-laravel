<x-layouts.employee>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $survey->titre }}</h1>
                <p class="text-gray-500 mt-1">{{ $survey->description }}</p>
            </div>
            <a href="{{ route('employee.surveys.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour
            </a>
        </div>

        @if($hasResponded)
            <div class="bg-green-50 border border-green-200 rounded-lg p-6 text-center">
                <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="mt-4 text-lg font-medium text-green-800">Vous avez déjà répondu à ce sondage</p>
                <p class="mt-2 text-green-600">Merci pour votre participation !</p>
            </div>
        @else
            <!-- Survey Form -->
            <form action="{{ route('employee.surveys.respond', $survey) }}" method="POST" class="space-y-6">
                @csrf

                @foreach($survey->questions as $index => $question)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-start mb-4">
                            <span class="flex-shrink-0 w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-sm font-medium">
                                {{ $index + 1 }}
                            </span>
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-medium text-gray-900">
                                    {{ $question->question }}
                                    @if($question->is_required)
                                        <span class="text-red-500">*</span>
                                    @endif
                                </h3>
                            </div>
                        </div>

                        <div class="ml-12">
                            @if($question->type === 'text')
                                <textarea
                                    name="responses[{{ $question->id }}]"
                                    rows="3"
                                    {{ $question->is_required ? 'required' : '' }}
                                    class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 @error('responses.'.$question->id) border-red-500 @enderror"
                                    placeholder="Votre réponse...">{{ old('responses.'.$question->id) }}</textarea>

                            @elseif($question->type === 'choice')
                                <div class="space-y-2">
                                    @foreach($question->options as $option)
                                        <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                            <input type="radio"
                                                name="responses[{{ $question->id }}]"
                                                value="{{ $option }}"
                                                {{ $question->is_required ? 'required' : '' }}
                                                {{ old('responses.'.$question->id) === $option ? 'checked' : '' }}
                                                class="text-green-600 focus:ring-green-500">
                                            <span class="ml-3 text-gray-700">{{ $option }}</span>
                                        </label>
                                    @endforeach
                                </div>

                            @elseif($question->type === 'rating')
                                <div class="flex items-center space-x-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <label class="cursor-pointer">
                                            <input type="radio"
                                                name="responses[{{ $question->id }}]"
                                                value="{{ $i }}"
                                                {{ $question->is_required ? 'required' : '' }}
                                                {{ old('responses.'.$question->id) == $i ? 'checked' : '' }}
                                                class="sr-only peer">
                                            <div class="w-12 h-12 border-2 border-gray-300 rounded-lg flex items-center justify-center text-lg font-medium text-gray-500 peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-600 hover:border-green-300 transition-colors">
                                                {{ $i }}
                                            </div>
                                        </label>
                                    @endfor
                                    <span class="ml-4 text-sm text-gray-500">1 = Pas du tout, 5 = Très satisfait</span>
                                </div>

                            @elseif($question->type === 'yesno')
                                <div class="flex items-center space-x-4">
                                    <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer flex-1 justify-center">
                                        <input type="radio"
                                            name="responses[{{ $question->id }}]"
                                            value="Oui"
                                            {{ $question->is_required ? 'required' : '' }}
                                            {{ old('responses.'.$question->id) === 'Oui' ? 'checked' : '' }}
                                            class="text-green-600 focus:ring-green-500">
                                        <span class="ml-2 text-gray-700 font-medium">Oui</span>
                                    </label>
                                    <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer flex-1 justify-center">
                                        <input type="radio"
                                            name="responses[{{ $question->id }}]"
                                            value="Non"
                                            {{ $question->is_required ? 'required' : '' }}
                                            {{ old('responses.'.$question->id) === 'Non' ? 'checked' : '' }}
                                            class="text-green-600 focus:ring-green-500">
                                        <span class="ml-2 text-gray-700 font-medium">Non</span>
                                    </label>
                                </div>
                            @endif

                            @error('responses.'.$question->id)
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                @endforeach

                <!-- Submit -->
                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('employee.surveys.index') }}" class="px-4 py-2 text-gray-700 hover:text-gray-900">Annuler</a>
                    <button type="submit" class="px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                        Soumettre mes réponses
                    </button>
                </div>
            </form>
        @endif
    </div>
</x-layouts.employee>
