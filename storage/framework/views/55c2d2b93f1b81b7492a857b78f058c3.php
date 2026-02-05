<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="space-y-6">
        <!-- Header comme sur tasks -->
        <div class="relative overflow-hidden rounded-2xl shadow-xl animate-fade-in-up" style="background: linear-gradient(135deg, #5680E9, #84CEEB) !important;">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            
            <div class="relative p-6 md:p-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <nav class="flex mb-3" aria-label="Breadcrumb">
                            <ol class="inline-flex items-center space-x-1">
                                <li><a href="<?php echo e(route('admin.dashboard')); ?>" class="text-white/70 hover:text-white text-sm">Dashboard</a></li>
                                <li><span class="text-white/50 mx-2">/</span></li>
                                <li><span class="text-white text-sm font-medium">Documents</span></li>
                            </ol>
                        </nav>
                        <h1 class="text-2xl md:text-3xl font-bold text-white flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                </svg>
                            </div>
                            Gestion des Documents
                        </h1>
                        <p class="text-white/80 mt-2">Contrats et documents RH des employés</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="<?php echo e(route('admin.document-requests.index')); ?>"
                           class="px-4 py-2.5 font-semibold rounded-xl transition-all shadow-lg flex items-center" style="background-color: rgba(255, 255, 255, 0.9); color: #F59E0B;">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8m-5 5h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293h3.172a1 1 0 00.707-.293l2.414-2.414a1 1 0 01.707-.293H20"/>
                            </svg>
                            Demandes
                            <?php $pendingCount = \App\Models\DocumentRequest::pending()->count(); ?>
                            <?php if($pendingCount > 0): ?>
                                <span class="ml-2 bg-amber-500 text-white text-xs font-bold px-2 py-0.5 rounded-full"><?php echo e($pendingCount); ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="<?php echo e(route('admin.global-documents.index')); ?>"
                           class="px-4 py-2.5 bg-white font-semibold rounded-xl hover:bg-purple-50 transition-all shadow-lg flex items-center" style="color: #5680E9;">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Docs Globaux
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 animate-fade-in-up animation-delay-100">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: rgba(86, 128, 233, 0.15);">
                        <svg class="w-6 h-6" style="color: #5680E9;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold" style="color: #5680E9;"><?php echo e($stats['total']); ?></p>
                        <p class="text-sm text-gray-500">Total documents</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: rgba(90, 185, 234, 0.15);">
                        <svg class="w-6 h-6" style="color: #5AB9EA;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold" style="color: #5AB9EA;"><?php echo e($stats['contracts'] ?? 0); ?></p>
                        <p class="text-sm text-gray-500">Contrats</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: rgba(136, 96, 208, 0.15);">
                        <svg class="w-6 h-6" style="color: #8860D0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold" style="color: #8860D0;"><?php echo e($employees->count()); ?></p>
                        <p class="text-sm text-gray-500">Employés</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 animate-fade-in-up animation-delay-200">
            <form method="GET" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" 
                           placeholder="Rechercher..."
                           class="w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <select name="type" class="rounded-xl border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Tous les types</option>
                    <?php $__currentLoopData = $documentTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($type->slug); ?>" <?php echo e(request('type') === $type->slug ? 'selected' : ''); ?>>
                            <?php echo e($type->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select name="user_id" class="rounded-xl border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Tous les employés</option>
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($emp->id); ?>" <?php echo e(request('user_id') == $emp->id ? 'selected' : ''); ?>>
                            <?php echo e($emp->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors">
                    Filtrer
                </button>
                <?php if(request()->hasAny(['search', 'type', 'user_id'])): ?>
                    <a href="<?php echo e(route('admin.documents.index')); ?>" class="px-4 py-2 text-gray-500 hover:text-gray-700">
                        Réinitialiser
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Employees with Documents Status -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in-up animation-delay-300">
            <div class="p-4 border-b border-gray-100 flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(86, 128, 233, 0.15);">
                    <svg class="w-4 h-4" style="color: #5680E9;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h2 class="font-semibold text-gray-900">Documents par Employé</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Employé</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Contrat</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Règlement</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">CV</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $empDocs = $documents->where('user_id', $employee->id);
                                // Contract document now on Contract model
                                $contract = $employee->currentContract;
                                $hasContractDoc = $contract && $contract->document_path;
                                // Règlement via global document acknowledgment
                                $hasAcknowledgedRules = $activeReglement ? $activeReglement->isAcknowledgedBy($employee) : false;
                                $hasCV = $empDocs->where('type.slug', 'cv')->first();
                            ?>
                            <tr class="hover:bg-purple-50/50 transition-colors group">
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold" style="background: linear-gradient(135deg, #5680E9, #84CEEB);">
                                            <?php echo e(strtoupper(substr($employee->name, 0, 1))); ?>

                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900"><?php echo e($employee->name); ?></p>
                                            <p class="text-sm text-gray-500"><?php echo e($employee->position->name ?? 'Non défini'); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <?php if($hasContractDoc): ?>
                                        <a href="<?php echo e(route('admin.employees.contract.download', $employee)); ?>" 
                                           class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full transition-colors" style="background-color: rgba(90, 185, 234, 0.15); color: #5680E9;">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                            Télécharger
                                        </a>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full bg-red-50 text-red-600">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            Manquant
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <?php if(!$activeReglement): ?>
                                        <span class="inline-flex items-center px-2.5 py-1 bg-gray-100 text-gray-500 text-xs font-medium rounded-full">
                                            — Aucun
                                        </span>
                                    <?php elseif($hasAcknowledgedRules): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full" style="background-color: rgba(90, 185, 234, 0.15); color: #5AB9EA;">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Accusé reçu
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full bg-amber-50 text-amber-600">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            En attente
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <?php if($hasCV): ?>
                                        <a href="<?php echo e(route('admin.documents.download', $hasCV)); ?>" 
                                           class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full transition-colors" style="background-color: rgba(90, 185, 234, 0.15); color: #5680E9;">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                            Télécharger
                                        </a>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full bg-red-50 text-red-600">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            Manquant
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <a href="<?php echo e(route('admin.employees.show', $employee)); ?>?from=documents" 
                                       class="inline-flex items-center px-3 py-1.5 text-white text-sm font-medium rounded-xl shadow-lg transition-all opacity-0 group-hover:opacity-100" style="background: linear-gradient(135deg, #5680E9, #5AB9EA);">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Gérer
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4" style="background-color: rgba(86, 128, 233, 0.15);">
                                            <svg class="w-8 h-8" style="color: #5680E9;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                        </div>
                                        <p class="text-gray-500">Aucun employé trouvé.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- All Documents List -->
        <?php if($documents->count() > 0): ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in-up animation-delay-400">
            <div class="p-4 border-b border-gray-100 flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(132, 206, 235, 0.15);">
                    <svg class="w-4 h-4" style="color: #84CEEB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h2 class="font-semibold text-gray-900">Tous les Documents</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Document</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Employé</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-purple-50/50 transition-colors group">
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: rgba(86, 128, 233, 0.15);">
                                            <svg class="w-5 h-5" style="color: #5680E9;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900"><?php echo e($document->title); ?></p>
                                            <p class="text-sm text-gray-500"><?php echo e($document->original_filename); ?> • <?php echo e($document->file_size_formatted); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-gray-900"><?php echo e($document->user->name); ?></span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full" style="background-color: rgba(136, 96, 208, 0.15); color: #8860D0;">
                                        <?php echo e($document->type->name); ?>

                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-500">
                                    <?php echo e($document->created_at->format('d/m/Y')); ?>

                                </td>
                                <td class="px-4 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="<?php echo e(route('admin.documents.download', $document)); ?>" 
                                           class="p-2 text-gray-400 hover:text-indigo-600 rounded-lg hover:bg-indigo-50 transition-colors"
                                           title="Télécharger">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                        </a>
                                        <form action="<?php echo e(route('admin.documents.destroy', $document)); ?>" method="POST" 
                                              onsubmit="return confirm('Supprimer ce document ?')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" 
                                                    class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition-colors"
                                                    title="Supprimer">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3)): ?>
<?php $attributes = $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3; ?>
<?php unset($__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc8c9fd5d7827a77a31381de67195f0c3)): ?>
<?php $component = $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3; ?>
<?php unset($__componentOriginalc8c9fd5d7827a77a31381de67195f0c3); ?>
<?php endif; ?>
<?php /**PATH D:\ManageX\resources\views/admin/documents/index.blade.php ENDPATH**/ ?>