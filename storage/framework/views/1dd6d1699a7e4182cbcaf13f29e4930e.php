<?php if (isset($component)) { $__componentOriginal09d149b94538c2315f503a5e890f2640 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal09d149b94538c2315f503a5e890f2640 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.employee','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.employee'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900"><?php echo e($survey->titre); ?></h1>
                <p class="text-gray-500 mt-1"><?php echo e($survey->description); ?></p>
            </div>
            <a href="<?php echo e(route('employee.surveys.index')); ?>" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour
            </a>
        </div>

        <?php if($hasResponded): ?>
            <div class="bg-green-50 border border-green-200 rounded-lg p-6 text-center">
                <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="mt-4 text-lg font-medium text-green-800">Vous avez déjà répondu à ce sondage</p>
                <p class="mt-2 text-green-600">Merci pour votre participation !</p>
            </div>
        <?php else: ?>
            <!-- Survey Form -->
            <form action="<?php echo e(route('employee.surveys.respond', $survey)); ?>" method="POST" class="space-y-6">
                <?php echo csrf_field(); ?>

                <?php $__currentLoopData = $survey->questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-start mb-4">
                            <span class="flex-shrink-0 w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-sm font-medium">
                                <?php echo e($index + 1); ?>

                            </span>
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-medium text-gray-900">
                                    <?php echo e($question->question); ?>

                                    <?php if($question->is_required): ?>
                                        <span class="text-red-500">*</span>
                                    <?php endif; ?>
                                </h3>
                            </div>
                        </div>

                        <div class="ml-12">
                            <?php if($question->type === 'text'): ?>
                                <textarea
                                    name="responses[<?php echo e($question->id); ?>]"
                                    rows="3"
                                    <?php echo e($question->is_required ? 'required' : ''); ?>

                                    class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 <?php $__errorArgs = ['responses.'.$question->id];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    placeholder="Votre réponse..."><?php echo e(old('responses.'.$question->id)); ?></textarea>

                            <?php elseif($question->type === 'choice'): ?>
                                <div class="space-y-2">
                                    <?php $__currentLoopData = $question->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                            <input type="radio"
                                                name="responses[<?php echo e($question->id); ?>]"
                                                value="<?php echo e($option); ?>"
                                                <?php echo e($question->is_required ? 'required' : ''); ?>

                                                <?php echo e(old('responses.'.$question->id) === $option ? 'checked' : ''); ?>

                                                class="text-green-600 focus:ring-green-500">
                                            <span class="ml-3 text-gray-700"><?php echo e($option); ?></span>
                                        </label>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>

                            <?php elseif($question->type === 'rating'): ?>
                                <div class="flex items-center space-x-2">
                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                        <label class="cursor-pointer">
                                            <input type="radio"
                                                name="responses[<?php echo e($question->id); ?>]"
                                                value="<?php echo e($i); ?>"
                                                <?php echo e($question->is_required ? 'required' : ''); ?>

                                                <?php echo e(old('responses.'.$question->id) == $i ? 'checked' : ''); ?>

                                                class="sr-only peer">
                                            <div class="w-12 h-12 border-2 border-gray-300 rounded-lg flex items-center justify-center text-lg font-medium text-gray-500 peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-600 hover:border-green-300 transition-colors">
                                                <?php echo e($i); ?>

                                            </div>
                                        </label>
                                    <?php endfor; ?>
                                    <span class="ml-4 text-sm text-gray-500">1 = Pas du tout, 5 = Très satisfait</span>
                                </div>

                            <?php elseif($question->type === 'yesno'): ?>
                                <div class="flex items-center space-x-4">
                                    <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer flex-1 justify-center">
                                        <input type="radio"
                                            name="responses[<?php echo e($question->id); ?>]"
                                            value="Oui"
                                            <?php echo e($question->is_required ? 'required' : ''); ?>

                                            <?php echo e(old('responses.'.$question->id) === 'Oui' ? 'checked' : ''); ?>

                                            class="text-green-600 focus:ring-green-500">
                                        <span class="ml-2 text-gray-700 font-medium">Oui</span>
                                    </label>
                                    <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer flex-1 justify-center">
                                        <input type="radio"
                                            name="responses[<?php echo e($question->id); ?>]"
                                            value="Non"
                                            <?php echo e($question->is_required ? 'required' : ''); ?>

                                            <?php echo e(old('responses.'.$question->id) === 'Non' ? 'checked' : ''); ?>

                                            class="text-green-600 focus:ring-green-500">
                                        <span class="ml-2 text-gray-700 font-medium">Non</span>
                                    </label>
                                </div>
                            <?php endif; ?>

                            <?php $__errorArgs = ['responses.'.$question->id];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <!-- Submit -->
                <div class="flex items-center justify-end space-x-4">
                    <a href="<?php echo e(route('employee.surveys.index')); ?>" class="px-4 py-2 text-gray-700 hover:text-gray-900">Annuler</a>
                    <button type="submit" class="px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                        Soumettre mes réponses
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal09d149b94538c2315f503a5e890f2640)): ?>
<?php $attributes = $__attributesOriginal09d149b94538c2315f503a5e890f2640; ?>
<?php unset($__attributesOriginal09d149b94538c2315f503a5e890f2640); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal09d149b94538c2315f503a5e890f2640)): ?>
<?php $component = $__componentOriginal09d149b94538c2315f503a5e890f2640; ?>
<?php unset($__componentOriginal09d149b94538c2315f503a5e890f2640); ?>
<?php endif; ?>
<?php /**PATH D:\ManageX\resources\views\employee\surveys\show.blade.php ENDPATH**/ ?>