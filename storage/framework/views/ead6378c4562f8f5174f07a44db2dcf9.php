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
    <div class="w-full mx-auto space-y-6">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in-up">
            <div class="px-6 py-8" style="background-color: #3B8BEB;">
                <div class="flex items-center gap-6">
                    <!-- Avatar -->
                    <div class="relative">
                        <div class="w-24 h-24 rounded-full bg-white/20 flex items-center justify-center overflow-hidden border-4 border-white/30">
                            <?php if($user->avatar): ?>
                                <img src="<?php echo e(avatar_url($user->avatar)); ?>" alt="<?php echo e($user->name); ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <span class="text-3xl font-bold text-white"><?php echo e(strtoupper(substr($user->name, 0, 2))); ?></span>
                            <?php endif; ?>
                        </div>
                        <button onclick="document.getElementById('avatarModal').classList.remove('hidden')" 
                                class="absolute bottom-0 right-0 p-2 bg-white rounded-full shadow-lg hover:bg-gray-100 transition-colors">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </button>
                    </div>
                    <!-- Info -->
                    <div class="text-white">
                        <h1 class="text-2xl font-bold"><?php echo e($user->name); ?></h1>
                        <p class="text-blue-100" style="color: rgba(255, 255, 255, 0.9);"><?php echo e($user->position?->name ?? $user->poste ?? 'Employé'); ?></p>
                        <p class="text-blue-200 text-sm mt-1" style="color: rgba(255, 255, 255, 0.7);"><?php echo e($user->department?->name ?? 'Non assigné'); ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Quick Stats -->
            <div class="grid grid-cols-3 divide-x divide-gray-100 bg-gray-50">
                <div class="px-6 py-4 text-center">
                    <p class="text-2xl font-bold text-gray-900"><?php echo e($user->leave_balance ?? 0); ?></p>
                    <p class="text-xs text-gray-500">Congés restants</p>
                </div>
                <div class="px-6 py-4 text-center">
                    <p class="text-2xl font-bold text-gray-900"><?php echo e($user->hire_date ? (int) $user->hire_date->diffInYears(now()) : '-'); ?></p>
                    <p class="text-xs text-gray-500">Années d'ancienneté</p>
                </div>
                <div class="px-6 py-4 text-center">
                    <p class="text-2xl font-bold text-gray-900">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?php echo e($user->status === 'active' ? 'text-white' : 'bg-gray-100 text-gray-800'); ?>"
                              style="<?php echo e($user->status === 'active' ? 'background-color: #3B8BEB;' : ''); ?>">
                            <?php echo e($user->status === 'active' ? 'Actif' : ($user->status ?? 'Actif')); ?>

                        </span>
                    </p>
                    <p class="text-xs text-gray-500">Statut</p>
                </div>
            </div>
        </div>

        <!-- Personal Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 animate-fade-in-up animation-delay-100">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900">Informations Personnelles</h2>
                <button onclick="document.getElementById('personalModal').classList.remove('hidden')" 
                        class="text-sm font-medium flex items-center gap-1 hover:opacity-80 transition-opacity" style="color: #3B8BEB;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    Modifier
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500">Nom complet</p>
                    <p class="font-medium text-gray-900"><?php echo e($user->name); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Email</p>
                    <p class="font-medium text-gray-900"><?php echo e($user->email); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Téléphone</p>
                    <p class="font-medium text-gray-900"><?php echo e($user->telephone ?? '-'); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Date de naissance</p>
                    <p class="font-medium text-gray-900"><?php echo e($user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->format('d/m/Y') : '-'); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Genre</p>
                    <p class="font-medium text-gray-900">
                        <?php if($user->gender === 'male'): ?> Homme
                        <?php elseif($user->gender === 'female'): ?> Femme
                        <?php elseif($user->gender === 'other'): ?> Autre
                        <?php else: ?> - <?php endif; ?>
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Adresse</p>
                    <p class="font-medium text-gray-900">
                        <?php if($user->address): ?>
                            <?php echo e($user->address); ?><br>
                            <?php echo e($user->postal_code); ?> <?php echo e($user->city); ?><br>
                            <?php echo e($user->country); ?>

                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Situation Familiale</p>
                    <p class="font-medium text-gray-900">
                        <?php switch($user->marital_status):
                            case ('single'): ?> Célibataire <?php break; ?>
                            <?php case ('married'): ?> Marié(e) <?php break; ?>
                            <?php case ('divorced'): ?> Divorcé(e) <?php break; ?>
                            <?php case ('widowed'): ?> Veuf/Veuve <?php break; ?>
                            <?php default: ?> -
                        <?php endswitch; ?>
                        (<?php echo e($user->children_count); ?> enfants)
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Numéro CNPS</p>
                    <p class="font-medium text-gray-900"><?php echo e($user->cnps_number ?? '-'); ?></p>
                </div>
            </div>
        </div>

        <!-- Emergency Contact -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 animate-fade-in-up animation-delay-200">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900">Contact d'Urgence</h2>
                <button onclick="document.getElementById('emergencyModal').classList.remove('hidden')" 
                        class="text-sm font-medium flex items-center gap-1 hover:opacity-80 transition-opacity" style="color: #3B8BEB;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    Modifier
                </button>
            </div>
            
            <?php if($user->emergency_contact_name): ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-sm text-gray-500">Nom</p>
                        <p class="font-medium text-gray-900"><?php echo e($user->emergency_contact_name); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Téléphone</p>
                        <p class="font-medium text-gray-900"><?php echo e($user->emergency_contact_phone ?? '-'); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Relation</p>
                        <p class="font-medium text-gray-900"><?php echo e($user->emergency_contact_relationship ?? '-'); ?></p>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <p>Aucun contact d'urgence renseigné</p>
                    <button onclick="document.getElementById('emergencyModal').classList.remove('hidden')" 
                            class="mt-4 hover:underline" style="color: #3B8BEB;">
                        Ajouter un contact
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Professional Information (Read-only) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 animate-fade-in-up animation-delay-300">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Informations Professionnelles</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500">Numéro employé</p>
                    <p class="font-medium text-gray-900"><?php echo e($user->employee_id ?? '-'); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Département</p>
                    <p class="font-medium text-gray-900"><?php echo e($user->department?->name ?? '-'); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Poste</p>
                    <p class="font-medium text-gray-900"><?php echo e($user->position?->name ?? $user->poste ?? '-'); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Type de contrat</p>
                    <p class="font-medium text-gray-900"><?php echo e($user->contract_type ?? '-'); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Date d'embauche</p>
                    <p class="font-medium text-gray-900"><?php echo e($user->hire_date ? \Carbon\Carbon::parse($user->hire_date)->format('d/m/Y') : '-'); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Fin de contrat</p>
                    <p class="font-medium text-gray-900"><?php echo e($user->contract_end_date ? \Carbon\Carbon::parse($user->contract_end_date)->format('d/m/Y') : 'CDI'); ?></p>
                </div>
            </div>
            
            <p class="text-xs text-gray-400 mt-4">Ces informations sont gérées par les RH et ne peuvent pas éªtre modifiées.</p>
        </div>

        <!-- Security -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 animate-fade-in-up animation-delay-400">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900">Sécurité</h2>
            </div>
            
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                    <p class="font-medium text-gray-900">Mot de passe</p>
                    <p class="text-sm text-gray-500">Derniére modification : <?php echo e($user->updated_at->diffForHumans()); ?></p>
                </div>
                <button onclick="document.getElementById('passwordModal').classList.remove('hidden')" 
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Modifier
                </button>
            </div>
        </div>
    </div>

    <!-- Personal Info Modal -->
    <div id="personalModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center overflow-y-auto py-8">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full mx-4 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Modifier les informations personnelles</h3>
            <form action="<?php echo e(route('employee.profile.update.personal')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="space-y-4 max-h-[60vh] overflow-y-auto">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom complet</label>
                        <input type="text" name="name" value="<?php echo e($user->name); ?>" required
                               class="w-full rounded-lg border-gray-300 focus:ring-opacity-50 focus:border-[#3B8BEB] focus:ring-[#3B8BEB]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                        <input type="tel" name="telephone" value="<?php echo e($user->telephone); ?>"
                               class="w-full rounded-lg border-gray-300 focus:border-[#3B8BEB] focus:ring-[#3B8BEB]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date de naissance</label>
                        <input type="date" name="date_of_birth" value="<?php echo e(old('date_of_birth', $user->date_of_birth?->format('Y-m-d'))); ?>"
                               class="w-full rounded-lg border-gray-300 focus:border-[#3B8BEB] focus:ring-[#3B8BEB]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Genre</label>
                        <select name="gender" class="w-full rounded-lg border-gray-300 focus:border-[#3B8BEB] focus:ring-[#3B8BEB]">
                            <option value="">Non spécifié</option>
                            <option value="male" <?php echo e($user->gender === 'male' ? 'selected' : ''); ?>>Homme</option>
                            <option value="female" <?php echo e($user->gender === 'female' ? 'selected' : ''); ?>>Femme</option>
                            <option value="other" <?php echo e($user->gender === 'other' ? 'selected' : ''); ?>>Autre</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                        <input type="text" name="address" value="<?php echo e($user->address); ?>"
                               class="w-full rounded-lg border-gray-300 focus:border-[#3B8BEB] focus:ring-[#3B8BEB]">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Code postal</label>
                            <input type="text" name="postal_code" value="<?php echo e($user->postal_code); ?>"
                                   class="w-full rounded-lg border-gray-300 focus:border-[#3B8BEB] focus:ring-[#3B8BEB]">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                            <input type="text" name="city" value="<?php echo e($user->city); ?>"
                                   class="w-full rounded-lg border-gray-300 focus:border-[#3B8BEB] focus:ring-[#3B8BEB]">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pays</label>
                        <input type="text" name="country" value="<?php echo e($user->country ?? 'France'); ?>"
                               class="w-full rounded-lg border-gray-300 focus:border-[#3B8BEB] focus:ring-[#3B8BEB]">
                    </div>
                    <div class="pt-4 border-t border-gray-100">
                        <h4 class="font-medium text-gray-900 mb-3">Informations Fiscales</h4>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Situation familiale</label>
                                <select name="marital_status" class="w-full rounded-lg border-gray-300 focus:border-[#3B8BEB] focus:ring-[#3B8BEB]">
                                    <option value="single" <?php echo e($user->marital_status === 'single' ? 'selected' : ''); ?>>Célibataire</option>
                                    <option value="married" <?php echo e($user->marital_status === 'married' ? 'selected' : ''); ?>>Marié(e)</option>
                                    <option value="divorced" <?php echo e($user->marital_status === 'divorced' ? 'selected' : ''); ?>>Divorcé(e)</option>
                                    <option value="widowed" <?php echo e($user->marital_status === 'widowed' ? 'selected' : ''); ?>>Veuf/Veuve</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Enfants</label>
                                    <input type="number" name="children_count" value="<?php echo e($user->children_count); ?>" min="0"
                                           class="w-full rounded-lg border-gray-300 focus:border-[#3B8BEB] focus:ring-[#3B8BEB]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">NÂ° CNPS</label>
                                    <input type="text" name="cnps_number" value="<?php echo e($user->cnps_number); ?>"
                                           class="w-full rounded-lg border-gray-300 focus:border-[#3B8BEB] focus:ring-[#3B8BEB]">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('personalModal').classList.add('hidden')" 
                            class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Annuler
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 text-white rounded-lg hover:opacity-90 transition-opacity" style="background-color: #3B8BEB;">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Emergency Contact Modal -->
    <div id="emergencyModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Contact d'urgence</h3>
            <form action="<?php echo e(route('employee.profile.update.emergency')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom du contact</label>
                        <input type="text" name="emergency_contact_name" value="<?php echo e($user->emergency_contact_name); ?>"
                               class="w-full rounded-lg border-gray-300 focus:border-[#3B8BEB] focus:ring-[#3B8BEB]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                        <input type="tel" name="emergency_contact_phone" value="<?php echo e($user->emergency_contact_phone); ?>"
                               class="w-full rounded-lg border-gray-300 focus:border-[#3B8BEB] focus:ring-[#3B8BEB]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Relation (ex: Conjoint, Parent)</label>
                        <input type="text" name="emergency_contact_relationship" value="<?php echo e($user->emergency_contact_relationship); ?>"
                               class="w-full rounded-lg border-gray-300 focus:border-[#3B8BEB] focus:ring-[#3B8BEB]">
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('emergencyModal').classList.add('hidden')" 
                            class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Annuler
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 text-white rounded-lg hover:opacity-90 transition-opacity" style="background-color: #3B8BEB;">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Avatar Modal -->
    <div id="avatarModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Changer la photo de profil</h3>
            <form action="<?php echo e(route('employee.profile.update.avatar')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="space-y-4">
                    <div class="flex justify-center">
                        <div class="w-32 h-32 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden" id="avatarPreview">
                            <?php if($user->avatar): ?>
                                <img src="<?php echo e(avatar_url($user->avatar)); ?>" alt="Preview" class="w-full h-full object-cover">
                            <?php else: ?>
                                <span class="text-4xl font-bold text-gray-400"><?php echo e(strtoupper(substr($user->name, 0, 2))); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sélectionner une image</label>
                        <input type="file" name="avatar" accept="image/*" required
                               onchange="previewAvatar(this)"
                               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-xs text-gray-500 mt-1">JPG, PNG, GIF ou WebP. Max 2MB.</p>
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('avatarModal').classList.add('hidden')" 
                            class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Annuler
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 text-white rounded-lg hover:opacity-90 transition-opacity" style="background-color: #3B8BEB;">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Password Modal -->
    <div id="passwordModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Modifier le mot de passe</h3>
            <form action="<?php echo e(route('employee.profile.update.password')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe actuel</label>
                        <input type="password" name="current_password" required
                               class="w-full rounded-lg border-gray-300 focus:border-[#3B8BEB] focus:ring-[#3B8BEB]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nouveau mot de passe</label>
                        <input type="password" name="password" required minlength="8"
                               class="w-full rounded-lg border-gray-300 focus:border-[#3B8BEB] focus:ring-[#3B8BEB]">
                        <p class="text-xs text-gray-500 mt-1">Min 8 caractéres, majuscule, minuscule et chiffre</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirmer le mot de passe</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full rounded-lg border-gray-300 focus:border-[#3B8BEB] focus:ring-[#3B8BEB]">
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('passwordModal').classList.add('hidden')" 
                            class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Annuler
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 text-white rounded-lg hover:opacity-90 transition-opacity" style="background-color: #3B8BEB;">
                        Modifier
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script nonce="<?php echo e($cspNonce ?? ''); ?>">
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatarPreview').innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover" alt="Preview">`;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
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
<?php /**PATH D:\ManageX\resources\views/employee/profile/index.blade.php ENDPATH**/ ?>