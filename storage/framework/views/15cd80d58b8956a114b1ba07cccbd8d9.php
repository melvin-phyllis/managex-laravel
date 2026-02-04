<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Évaluation - <?php echo e($evaluation->intern->name ?? 'Stagiaire'); ?> - <?php echo e($evaluation->week_label); ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            padding: 20px;
        }
        .header {
            border-bottom: 3px solid #8b5cf6;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #8b5cf6;
        }
        .company-subtitle {
            color: #666;
            font-size: 14px;
        }
        .document-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin: 20px 0;
            padding: 15px;
            background: linear-gradient(135deg, #f3e8ff 0%, #ede9fe 100%);
            border-radius: 8px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 25px;
            border-collapse: collapse;
        }
        .info-table td {
            width: 50%;
            padding: 15px;
            vertical-align: top;
            border: 1px solid #e5e7eb;
        }
        .info-box h3 {
            font-size: 13px;
            color: #8b5cf6;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row {
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            color: #374151;
        }
        .score-summary {
            background: linear-gradient(135deg, #8b5cf6 0%, #a855f7 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            text-align: center;
        }
        .score-big {
            font-size: 48px;
            font-weight: bold;
        }
        .grade-letter {
            font-size: 28px;
            font-weight: bold;
            margin-left: 20px;
        }
        .criteria-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .criteria-table th {
            background-color: #8b5cf6;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        .criteria-table td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        .criteria-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .score-cell {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
        }
        .score-good { color: #059669; }
        .score-medium { color: #d97706; }
        .score-low { color: #dc2626; }
        .progress-bar {
            width: 100%;
            height: 8px;
            background-color: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background-color: #8b5cf6;
        }
        .comment-section {
            margin: 20px 0;
            padding: 15px;
            background-color: #faf5ff;
            border-left: 4px solid #8b5cf6;
            border-radius: 0 5px 5px 0;
        }
        .comment-title {
            font-weight: bold;
            color: #7c3aed;
            margin-bottom: 8px;
        }
        .objectives-section {
            margin: 20px 0;
            padding: 15px;
            background-color: #f0fdf4;
            border-left: 4px solid #22c55e;
            border-radius: 0 5px 5px 0;
        }
        .objectives-title {
            font-weight: bold;
            color: #16a34a;
            margin-bottom: 8px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 10px;
        }
        .grade-badge {
            display: inline-block;
            padding: 4px 15px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            margin-left: 10px;
        }
        .grade-a { background-color: #d1fae5; color: #065f46; }
        .grade-b { background-color: #dbeafe; color: #1e40af; }
        .grade-c { background-color: #fef3c7; color: #92400e; }
        .grade-d { background-color: #ffedd5; color: #c2410c; }
        .grade-e { background-color: #fee2e2; color: #b91c1c; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">ManageX</div>
        <div class="company-subtitle">Suivi des Stagiaires - Évaluation Hebdomadaire</div>
    </div>

    <div class="document-title">
        ÉVALUATION HEBDOMADAIRE
        <br>
        <span style="font-size: 14px; font-weight: normal;"><?php echo e($evaluation->week_label); ?></span>
    </div>

    <table class="info-table">
        <tr>
            <td>
                <div class="info-box">
                    <h3>STAGIAIRE</h3>
                    <div class="info-row"><span class="info-label">Nom:</span> <?php echo e($intern->name); ?></div>
                    <div class="info-row"><span class="info-label">Email:</span> <?php echo e($intern->email); ?></div>
                    <div class="info-row"><span class="info-label">Département:</span> <?php echo e($intern->department->name ?? 'Non assigné'); ?></div>
                </div>
            </td>
            <td>
                <div class="info-box">
                    <h3>TUTEUR</h3>
                    <div class="info-row"><span class="info-label">Nom:</span> <?php echo e($tutor->name); ?></div>
                    <div class="info-row"><span class="info-label">Email:</span> <?php echo e($tutor->email); ?></div>
                    <div class="info-row"><span class="info-label">Date d'évaluation:</span> <?php echo e($evaluation->submitted_at?->format('d/m/Y') ?? 'N/A'); ?></div>
                </div>
            </td>
        </tr>
    </table>

    <div class="score-summary">
        <span class="score-big"><?php echo e($evaluation->total_score); ?>/10</span>
        <span class="grade-letter">Grade <?php echo e($evaluation->grade_letter); ?></span>
        <span class="grade-badge grade-<?php echo e(strtolower($evaluation->grade_letter)); ?>">
            <?php
                $labels = ['A' => 'Excellent', 'B' => 'Bien', 'C' => 'Satisfaisant', 'D' => 'À améliorer', 'E' => 'Insuffisant'];
            ?>
            <?php echo e($labels[$evaluation->grade_letter] ?? 'N/A'); ?>

        </span>
    </div>

    <table class="criteria-table">
        <thead>
            <tr>
                <th style="width: 25%;">Critère</th>
                <th style="width: 40%;">Description</th>
                <th style="width: 15%; text-align: center;">Score</th>
                <th style="width: 20%;">Progression</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $criteria = [
                    'discipline' => ['label' => 'Discipline', 'desc' => 'Respect des horaires, assiduité'],
                    'behavior' => ['label' => 'Comportement', 'desc' => 'Attitude professionnelle'],
                    'skills' => ['label' => 'Compétences', 'desc' => 'Qualité du travail, progression'],
                    'communication' => ['label' => 'Communication', 'desc' => 'Clarté, écoute, reporting'],
                ];
            ?>
            <?php $__currentLoopData = $criteria; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $score = $evaluation->{$key.'_score'};
                    $percent = ($score / 2.5) * 100;
                    $colorClass = $score >= 2 ? 'score-good' : ($score >= 1 ? 'score-medium' : 'score-low');
                ?>
                <tr>
                    <td><strong><?php echo e($info['label']); ?></strong></td>
                    <td><?php echo e($info['desc']); ?></td>
                    <td class="score-cell <?php echo e($colorClass); ?>"><?php echo e($score); ?>/2.5</td>
                    <td>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo e($percent); ?>%"></div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <?php if($evaluation->general_comment): ?>
        <div class="comment-section">
            <div class="comment-title">Bilan de la semaine</div>
            <p><?php echo e($evaluation->general_comment); ?></p>
        </div>
    <?php endif; ?>

    <?php if($evaluation->objectives_next_week): ?>
        <div class="objectives-section">
            <div class="objectives-title">Objectifs pour la semaine prochaine</div>
            <p><?php echo e($evaluation->objectives_next_week); ?></p>
        </div>
    <?php endif; ?>

    <div class="footer">
        <p>Document généré automatiquement par ManageX le <?php echo e(now()->format('d/m/Y à H:i')); ?></p>
        <p>Ce document est confidentiel et destiné uniquement aux personnes autorisées.</p>
        <p style="margin-top: 10px;">ManageX - Gestion des Ressources Humaines</p>
    </div>
</body>
</html>
<?php /**PATH D:\ManageX\resources\views\pdf\intern-evaluation.blade.php ENDPATH**/ ?>