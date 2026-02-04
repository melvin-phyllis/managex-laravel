<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport de Présences</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            padding: 15px;
        }
        .header {
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
        }
        .company-subtitle {
            color: #666;
            font-size: 12px;
        }
        .document-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin: 20px 0;
            padding: 10px;
            background-color: #f3f4f6;
            border-radius: 5px;
        }
        .filter-info {
            background-color: #eff6ff;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 11px;
        }
        .filter-info span {
            margin-right: 20px;
        }
        .filter-info strong {
            color: #2563eb;
        }
        .stats-row {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .stat-box {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 10px;
            border: 1px solid #e5e7eb;
            background-color: #f9fafb;
        }
        .stat-box:first-child {
            border-radius: 5px 0 0 5px;
        }
        .stat-box:last-child {
            border-radius: 0 5px 5px 0;
        }
        .stat-value {
            font-size: 20px;
            font-weight: bold;
            color: #2563eb;
        }
        .stat-label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
        }
        .presence-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .presence-table th {
            background-color: #2563eb;
            color: white;
            padding: 8px 10px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
        }
        .presence-table td {
            padding: 6px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
        }
        .presence-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .presence-table .time {
            font-family: 'Courier New', monospace;
            text-align: center;
        }
        .presence-table .duration {
            font-family: 'Courier New', monospace;
            text-align: center;
            font-weight: bold;
        }
        .status-present {
            color: #059669;
        }
        .status-absent {
            color: #dc2626;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 9px;
        }
        .page-break {
            page-break-after: always;
        }
        .employee-section {
            margin-bottom: 30px;
        }
        .employee-header {
            background-color: #1f2937;
            color: white;
            padding: 8px 12px;
            border-radius: 5px 5px 0 0;
            font-weight: bold;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">ManageX</div>
        <div class="company-subtitle">Gestion des Ressources Humaines</div>
    </div>

    <div class="document-title">
        RAPPORT DE PRÉSENCES
    </div>

    <div class="filter-info">
        <span><strong>Période:</strong> <?php echo e($filters['date_debut'] ?? 'Début'); ?> au <?php echo e($filters['date_fin'] ?? 'Aujourd\'hui'); ?></span>
        <?php if(isset($filters['employee']) && $filters['employee']): ?>
            <span><strong>Employé:</strong> <?php echo e($filters['employee_name'] ?? 'Tous'); ?></span>
        <?php endif; ?>
        <span><strong>Généré le:</strong> <?php echo e(now()->format('d/m/Y à H:i')); ?></span>
    </div>

    
    <table style="width: 100%; margin-bottom: 20px;">
        <tr>
            <td style="width: 25%; text-align: center; padding: 10px; border: 1px solid #e5e7eb; background-color: #f9fafb;">
                <div style="font-size: 20px; font-weight: bold; color: #2563eb;"><?php echo e($stats['total_presences'] ?? 0); ?></div>
                <div style="font-size: 10px; color: #6b7280; text-transform: uppercase;">Total Présences</div>
            </td>
            <td style="width: 25%; text-align: center; padding: 10px; border: 1px solid #e5e7eb; background-color: #f9fafb;">
                <div style="font-size: 20px; font-weight: bold; color: #059669;"><?php echo e($stats['total_employees'] ?? 0); ?></div>
                <div style="font-size: 10px; color: #6b7280; text-transform: uppercase;">Employés</div>
            </td>
            <td style="width: 25%; text-align: center; padding: 10px; border: 1px solid #e5e7eb; background-color: #f9fafb;">
                <div style="font-size: 20px; font-weight: bold; color: #7c3aed;"><?php echo e($stats['avg_hours'] ?? '0h'); ?></div>
                <div style="font-size: 10px; color: #6b7280; text-transform: uppercase;">Moyenne/Jour</div>
            </td>
            <td style="width: 25%; text-align: center; padding: 10px; border: 1px solid #e5e7eb; background-color: #f9fafb;">
                <div style="font-size: 20px; font-weight: bold; color: #ea580c;"><?php echo e($stats['total_hours'] ?? '0h'); ?></div>
                <div style="font-size: 10px; color: #6b7280; text-transform: uppercase;">Total Heures</div>
            </td>
        </tr>
    </table>

    
    <table class="presence-table">
        <thead>
            <tr>
                <th style="width: 25%;">Employé</th>
                <th style="width: 15%;">Date</th>
                <th style="width: 12%;">Arrivée</th>
                <th style="width: 12%;">Départ</th>
                <th style="width: 12%;">Durée</th>
                <th style="width: 24%;">Notes</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $presences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $presence): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($presence->user->name); ?></td>
                    <td><?php echo e($presence->date->format('d/m/Y')); ?></td>
                    <td class="time"><?php echo e($presence->check_in->format('H:i')); ?></td>
                    <td class="time"><?php echo e($presence->check_out ? $presence->check_out->format('H:i') : '-'); ?></td>
                    <td class="duration"><?php echo e($presence->duree ?? '-'); ?></td>
                    <td><?php echo e(Str::limit($presence->notes, 30)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="no-data">Aucune présence trouvée pour cette période.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if($presences->count() > 0): ?>
    <div style="text-align: right; font-size: 10px; color: #6b7280;">
        Total: <?php echo e($presences->count()); ?> enregistrement(s)
    </div>
    <?php endif; ?>

    <div class="footer">
        <p>Document généré automatiquement par ManageX le <?php echo e(now()->format('d/m/Y à H:i')); ?></p>
        <p>Ce rapport est confidentiel et destiné à un usage interne uniquement.</p>
        <p style="margin-top: 8px;">ManageX SAS - 123 Avenue de la République, 75011 Paris - contact@managex.com</p>
    </div>
</body>
</html>
<?php /**PATH D:\ManageX\resources\views\pdf\presences-report.blade.php ENDPATH**/ ?>