<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche de Paie - <?php echo e($payroll->periode); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            padding: 20px;
        }
        .header {
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #2563eb;
        }
        .company-subtitle {
            color: #666;
            font-size: 14px;
        }
        .document-title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            color: #1f2937;
            margin: 30px 0;
            padding: 10px;
            background-color: #f3f4f6;
            border-radius: 5px;
        }
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .info-box {
            display: table-cell;
            width: 48%;
            padding: 15px;
            border: 1px solid #e5e7eb;
            border-radius: 5px;
            vertical-align: top;
        }
        .info-box:first-child {
            margin-right: 4%;
        }
        .info-box h3 {
            font-size: 14px;
            color: #2563eb;
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
        .payroll-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .payroll-table th {
            background-color: #2563eb;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        .payroll-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        .payroll-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .payroll-table .amount {
            text-align: right;
            font-family: 'Courier New', monospace;
        }
        .total-section {
            margin-top: 20px;
            padding: 20px;
            background-color: #eff6ff;
            border: 2px solid #2563eb;
            border-radius: 5px;
        }
        .total-row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .total-label {
            display: table-cell;
            width: 70%;
            font-weight: bold;
            font-size: 14px;
        }
        .total-amount {
            display: table-cell;
            width: 30%;
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            color: #2563eb;
            font-family: 'Courier New', monospace;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }
        .status-paid {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .notes-section {
            margin-top: 20px;
            padding: 15px;
            background-color: #fefce8;
            border-left: 4px solid #eab308;
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
        BULLETIN DE PAIE
        <br>
        <span style="font-size: 14px; font-weight: normal;"><?php echo e($payroll->periode); ?></span>
    </div>

    <table style="width: 100%; margin-bottom: 30px;">
        <tr>
            <td style="width: 48%; vertical-align: top; padding-right: 10px;">
                <div style="padding: 15px; border: 1px solid #e5e7eb; border-radius: 5px;">
                    <h3 style="font-size: 14px; color: #2563eb; margin-bottom: 10px; padding-bottom: 5px; border-bottom: 1px solid #e5e7eb;">ENTREPRISE</h3>
                    <div style="margin-bottom: 5px;"><span style="font-weight: bold;">Raison sociale:</span> ManageX SAS</div>
                    <div style="margin-bottom: 5px;"><span style="font-weight: bold;">Adresse:</span> 123 Avenue de la République</div>
                    <div style="margin-bottom: 5px;">75011 Paris, France</div>
                    <div style="margin-bottom: 5px;"><span style="font-weight: bold;">SIRET:</span> 123 456 789 00012</div>
                </div>
            </td>
            <td style="width: 48%; vertical-align: top; padding-left: 10px;">
                <div style="padding: 15px; border: 1px solid #e5e7eb; border-radius: 5px;">
                    <h3 style="font-size: 14px; color: #2563eb; margin-bottom: 10px; padding-bottom: 5px; border-bottom: 1px solid #e5e7eb;">EMPLOYÉ</h3>
                    <div style="margin-bottom: 5px;"><span style="font-weight: bold;">Nom:</span> <?php echo e($payroll->user->name); ?></div>
                    <div style="margin-bottom: 5px;"><span style="font-weight: bold;">Email:</span> <?php echo e($payroll->user->email); ?></div>
                    <div style="margin-bottom: 5px;"><span style="font-weight: bold;">Poste:</span> <?php echo e($payroll->user->poste ?? 'Non défini'); ?></div>
                    <div style="margin-bottom: 5px;"><span style="font-weight: bold;">Téléphone:</span> <?php echo e($payroll->user->telephone ?? 'Non défini'); ?></div>
                </div>
            </td>
        </tr>
    </table>

    <table class="payroll-table">
        <thead>
            <tr>
                <th style="width: 60%;">Désignation</th>
                <th style="width: 20%;">Base</th>
                <th style="width: 20%; text-align: right;">Montant</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Salaire de base</td>
                <td>151.67 h</td>
                <td class="amount"><?php echo e(number_format($payroll->montant * 0.85, 2, ',', ' ')); ?> €</td>
            </tr>
            <tr>
                <td>Prime d'ancienneté</td>
                <td>-</td>
                <td class="amount"><?php echo e(number_format($payroll->montant * 0.05, 2, ',', ' ')); ?> €</td>
            </tr>
            <tr>
                <td>Prime de transport</td>
                <td>-</td>
                <td class="amount"><?php echo e(number_format($payroll->montant * 0.03, 2, ',', ' ')); ?> €</td>
            </tr>
            <tr>
                <td>Tickets restaurant</td>
                <td>20 jours</td>
                <td class="amount"><?php echo e(number_format($payroll->montant * 0.04, 2, ',', ' ')); ?> €</td>
            </tr>
            <tr>
                <td>Indemnités diverses</td>
                <td>-</td>
                <td class="amount"><?php echo e(number_format($payroll->montant * 0.03, 2, ',', ' ')); ?> €</td>
            </tr>
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row">
            <span class="total-label">SALAIRE NET À PAYER</span>
            <span class="total-amount"><?php echo e($payroll->montant_formatted); ?></span>
        </div>
        <div style="margin-top: 15px;">
            <span style="font-weight: bold;">Statut du paiement:</span>
            <span class="status-badge <?php echo e($payroll->statut === 'paid' ? 'status-paid' : 'status-pending'); ?>">
                <?php echo e($payroll->statut === 'paid' ? 'Payé' : 'En attente'); ?>

            </span>
        </div>
    </div>

    <?php if($payroll->notes): ?>
    <div class="notes-section">
        <strong>Notes:</strong> <?php echo e($payroll->notes); ?>

    </div>
    <?php endif; ?>

    <div class="footer">
        <p>Document généré automatiquement par ManageX le <?php echo e(now()->format('d/m/Y à H:i')); ?></p>
        <p>Ce bulletin de paie est confidentiel. Conservez-le précieusement.</p>
        <p style="margin-top: 10px;">ManageX SAS - 123 Avenue de la République, 75011 Paris - contact@managex.com</p>
    </div>
</body>
</html>
<?php /**PATH D:\ManageX\resources\views\pdf\payroll.blade.php ENDPATH**/ ?>