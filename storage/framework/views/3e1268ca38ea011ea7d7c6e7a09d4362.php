<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Bulletin de Paie - <?php echo e($user->name); ?></title>
    <style>
        /* CSS STYLES */
        :root {
            --border-color: #000;
            --highlight-red: #d32f2f;
            --bg-color: #fff;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            background-color: #fff;
            padding: 15px;
            font-size: 11px;
            margin: 0;
        }

        .payslip-container {
            width: 100%;
            background-color: var(--bg-color);
        }

        /* Helpers */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        .red-text { color: var(--highlight-red); }
        .uppercase { text-transform: uppercase; }
        
        /* Header Section */
        .header-table {
            width: 100%;
            margin-bottom: 10px;
        }
        .header-table td { vertical-align: top; }

        .logo-box {
            font-weight: bold;
            font-style: italic;
            font-size: 20px;
        }

        .logo-sub {
            font-size: 9px;
            display: block;
        }

        .title-box {
            text-align: center;
        }

        .title-box h1 {
            text-decoration: underline;
            font-size: 16px;
            margin: 10px 0 0 0;
        }

        .company-info {
            text-align: right;
            font-size: 10px;
            line-height: 1.4;
        }

        /* Employee Info Table */
        .employee-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid var(--border-color);
            margin-bottom: 0;
        }

        .employee-table th, .employee-table td {
            border: 1px solid var(--border-color);
            padding: 4px;
            text-align: center;
        }

        .employee-table th {
            font-size: 9px;
            font-weight: normal;
            background: #f9f9f9;
        }

        .employee-table td {
            font-weight: bold;
            font-size: 10px;
        }

        /* Main Table */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid var(--border-color);
            border-top: none;
        }

        .main-table th, .main-table td {
            border: 1px solid var(--border-color);
            padding: 4px;
            vertical-align: top;
        }

        .main-table th {
            text-transform: uppercase;
            font-size: 10px;
            padding: 6px 4px;
            background: #f5f5f5;
        }

        .col-code { width: 6%; }
        .col-rubrique { width: 34%; }
        .col-base { width: 12%; }
        .col-taux { width: 8%; }
        .col-retenues { width: 18%; }
        .col-gains { width: 18%; }

        .indent { padding-left: 15px; font-size: 10px; color: #555; }
        
        .totals-row td {
            font-weight: bold;
            color: var(--highlight-red);
            font-size: 11px;
            padding: 6px 4px;
        }

        .net-pay-cell {
            font-size: 14px;
            font-weight: bold;
            color: var(--highlight-red);
            padding: 8px !important;
        }

        /* Footer Accumulators */
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid var(--border-color);
            margin-top: 10px;
        }

        .footer-table th, .footer-table td {
            border: 1px solid var(--border-color);
            padding: 4px;
            text-align: center;
            font-size: 9px;
        }

        .footer-title {
            text-align: left;
            padding: 5px;
            font-weight: bold;
            background: #f9f9f9;
        }

        .footer-label { font-size: 9px; color: #333; }
        .footer-value { font-weight: bold; font-size: 10px; }

        .document-footer {
            margin-top: 10px;
            text-align: center;
            font-size: 8px;
            color: #666;
        }
    </style>
</head>
<body>

    <div class="payslip-container">
        <!-- HEADER -->
        <table class="header-table">
            <tr>
                <td style="width: 25%;">
                    <div class="logo-box">
                        <?php echo e(config('app.name', 'ManageX')); ?>

                        <span class="logo-sub">GESTION RH</span>
                    </div>
                </td>
                <td style="width: 40%;" class="title-box">
                    <h1>Bulletin de paie</h1>
                </td>
                <td style="width: 35%;" class="company-info">
                    <strong><?php echo e(config('app.name', 'ManageX')); ?></strong><br>
                    <?php echo e($user->company->address ?? 'Abidjan, Côte d\'Ivoire'); ?><br><br>
                    CNPS Employé : <?php echo e($user->cnps_number ?? 'N/A'); ?><br>
                    Bull. n°<?php echo e(str_pad($payroll->id, 4, '0', STR_PAD_LEFT)); ?> du <?php echo e($payroll->created_at->format('d/m/y')); ?>

                </td>
            </tr>
        </table>

        <!-- EMPLOYEE INFO -->
        <table class="employee-table">
            <tr>
                <th>Matricule</th>
                <th>Nom et Prénoms</th>
                <th>Parts</th>
                <th>Catégorie</th>
                <th>Emploi</th>
                <th>Section</th>
                <th>Date</th>
            </tr>
            <tr>
                <td><?php echo e($user->employee_id ?? $user->id); ?></td>
                <td><?php echo e(strtoupper($user->name)); ?></td>
                <td><?php echo e(number_format($payroll->fiscal_parts ?? 1, 1, ',', '')); ?></td>
                <td><?php echo e($user->category ?? 'M2'); ?></td>
                <td><?php echo e(strtoupper($user->position->name ?? $user->poste ?? '-')); ?></td>
                <td><?php echo e($user->section ?? '-'); ?></td>
                <td><?php echo e(str_pad($payroll->mois, 2, '0', STR_PAD_LEFT)); ?>/<?php echo e(substr($payroll->annee, 2)); ?></td>
            </tr>
        </table>

        <!-- MAIN PAYROLL TABLE -->
        <table class="main-table">
            <thead>
                <tr>
                    <th class="col-code">CODE</th>
                    <th class="col-rubrique">RUBRIQUE</th>
                    <th class="col-base">BASE</th>
                    <th class="col-taux">TAUX</th>
                    <th class="col-retenues">RETENUES</th>
                    <th class="col-gains">GAINS</th>
                </tr>
            </thead>
            <tbody>
                <!-- Salaire de Base -->
                <tr>
                    <td class="text-center">30</td>
                    <td>Salaire de Base Mensuel</td>
                    <td class="text-right"><?php echo e(number_format($contract->base_salary ?? $payroll->base_salary ?? 0, 0, ',', ' ')); ?></td>
                    <td class="text-center">30</td>
                    <td></td>
                    <td class="text-right"><?php echo e(number_format($contract->base_salary ?? $payroll->base_salary ?? 0, 0, ',', ' ')); ?></td>
                </tr>

                <!-- Primes imposables -->
                <?php if(($payroll->seniority_bonus ?? 0) > 0): ?>
                <tr>
                    <td class="text-center">32</td>
                    <td>Prime d'Ancienneté</td>
                    <td class="text-right"><?php echo e(number_format($payroll->seniority_bonus, 0, ',', ' ')); ?></td>
                    <td class="text-center">30</td>
                    <td></td>
                    <td class="text-right"><?php echo e(number_format($payroll->seniority_bonus, 0, ',', ' ')); ?></td>
                </tr>
                <?php endif; ?>

                <?php if(($payroll->housing_allowance ?? 0) > 0): ?>
                <tr>
                    <td class="text-center">35</td>
                    <td>Indemnité de Logement</td>
                    <td class="text-right"><?php echo e(number_format($payroll->housing_allowance, 0, ',', ' ')); ?></td>
                    <td class="text-center">30</td>
                    <td></td>
                    <td class="text-right"><?php echo e(number_format($payroll->housing_allowance, 0, ',', ' ')); ?></td>
                </tr>
                <?php endif; ?>

                <?php if(($payroll->bonuses ?? 0) > 0): ?>
                <tr>
                    <td class="text-center">40</td>
                    <td>Primes et Bonus</td>
                    <td class="text-right"><?php echo e(number_format($payroll->bonuses, 0, ',', ' ')); ?></td>
                    <td class="text-center"></td>
                    <td></td>
                    <td class="text-right"><?php echo e(number_format($payroll->bonuses, 0, ',', ' ')); ?></td>
                </tr>
                <?php endif; ?>

                <?php if(($payroll->overtime_amount ?? 0) > 0): ?>
                <tr>
                    <td class="text-center">50</td>
                    <td>Heures Supplémentaires</td>
                    <td class="text-right"><?php echo e(number_format($payroll->overtime_amount, 0, ',', ' ')); ?></td>
                    <td class="text-center"></td>
                    <td></td>
                    <td class="text-right"><?php echo e(number_format($payroll->overtime_amount, 0, ',', ' ')); ?></td>
                </tr>
                <?php endif; ?>

                <!-- Lignes HS vides (optionnel) -->
                <tr>
                    <td></td>
                    <td class="indent">
                        HS 15%<br>
                        HS 50%<br>
                        HS 75%<br>
                        HS 100%
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                
                <!-- TOTAL IMPOSABLE -->
                <tr class="totals-row">
                    <td class="text-center">360</td>
                    <td>TOTAL IMPOSABLE (1)</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-right"><?php echo e(number_format($payroll->taxable_gross, 0, ',', ' ')); ?></td>
                </tr>

                <!-- IMPÔTS ET RETENUES -->
                <tr>
                    <td class="text-center">370</td>
                    <td>IS (Impôt sur le Salaire)</td>
                    <td></td>
                    <td class="text-center">1,2 %</td>
                    <td class="text-right"><?php echo e(number_format($payroll->tax_is, 0, ',', ' ')); ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td>CN (Contribution Nationale)</td>
                    <td></td>
                    <td></td>
                    <td class="text-right"><?php echo e(number_format($payroll->tax_cn, 0, ',', ' ')); ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td>IGR (Impôt sur le Revenu Général)</td>
                    <td></td>
                    <td></td>
                    <td class="text-right"><?php echo e(number_format($payroll->tax_igr, 0, ',', ' ')); ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="text-center">565</td>
                    <td>CNPS (Cotisation Sociale CNPS)</td>
                    <td></td>
                    <td class="text-center">5,4 %</td>
                    <td class="text-right"><?php echo e(number_format($payroll->cnps_employee, 0, ',', ' ')); ?></td>
                    <td></td>
                </tr>

                <!-- TOTAL RETENUES -->
                <tr class="totals-row">
                    <td class="text-center">670</td>
                    <td>TOTAL RETENUES (2)</td>
                    <td></td>
                    <td></td>
                    <td class="text-right"><?php echo e(number_format($payroll->total_deductions, 0, ',', ' ')); ?></td>
                    <td></td>
                </tr>

                <!-- TOTAL NET -->
                <tr class="totals-row">
                    <td class="text-center">690</td>
                    <td>TOTAL NET</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-right"><?php echo e(number_format($payroll->taxable_gross - $payroll->total_deductions, 0, ',', ' ')); ?></td>
                </tr>

                <!-- INDEMNITÉ TRANSPORT (Non imposable) -->
                <?php if(($payroll->transport_allowance ?? 0) > 0): ?>
                <tr>
                    <td class="text-center">710</td>
                    <td>Indemnité de transport</td>
                    <td class="text-right"><?php echo e(number_format($payroll->transport_allowance, 0, ',', ' ')); ?></td>
                    <td class="text-center">30</td>
                    <td></td>
                    <td class="text-right"><?php echo e(number_format($payroll->transport_allowance, 0, ',', ' ')); ?></td>
                </tr>
                <?php endif; ?>

                <!-- Autres retenues diverses (acomptes, prêts, etc.) - placeholder -->
                <!--
                <tr>
                    <td></td>
                    <td>
                        Acomptes<br>
                        Prêts scolaire<br>
                        Assurance<br>
                        Pharmacie
                    </td>
                    <td></td>
                    <td></td>
                    <td class="text-right">
                        0<br>
                        0<br>
                        0<br>
                        0
                    </td>
                    <td></td>
                </tr>
                -->

                <!-- NET A PAYER -->
                <tr>
                    <td colspan="4" class="text-right net-pay-cell">NET A PAYER</td>
                    <td colspan="2" class="text-right net-pay-cell" style="font-size: 16px;"><?php echo e(number_format($payroll->net_salary, 0, ',', ' ')); ?></td>
                </tr>
            </tbody>
        </table>

        <!-- CUMULS ANNUELS -->
        <table class="footer-table">
            <tr>
                <td colspan="8" class="footer-title">
                    CUMULS ANNUELS : <span style="font-weight: normal;">récapitulatif des sommes retenues et perçues depuis le début de l'année</span>
                </td>
            </tr>
            <tr>
                <td class="footer-label">Jours</td>
                <td class="footer-label">Imposable</td>
                <td class="footer-label">IS</td>
                <td class="footer-label">CN</td>
                <td class="footer-label">IGR</td>
                <td class="footer-label">CNPS</td>
                <td class="footer-label">Congés Payés</td>
                <td class="footer-label">Congés Acquis</td>
            </tr>
            <tr>
                <td class="footer-value"><?php echo e($payroll->mois * 30); ?></td>
                <td class="footer-value"><?php echo e(number_format($payroll->taxable_gross * $payroll->mois, 0, ',', ' ')); ?></td>
                <td class="footer-value"><?php echo e(number_format($payroll->tax_is * $payroll->mois, 0, ',', ' ')); ?></td>
                <td class="footer-value"><?php echo e(number_format($payroll->tax_cn * $payroll->mois, 0, ',', ' ')); ?></td>
                <td class="footer-value"><?php echo e(number_format($payroll->tax_igr * $payroll->mois, 0, ',', ' ')); ?></td>
                <td class="footer-value"><?php echo e(number_format($payroll->cnps_employee * $payroll->mois, 0, ',', ' ')); ?></td>
                <td class="footer-value">30</td>
                <td class="footer-value"><?php echo e(number_format($payroll->mois * 2.2, 1, ',', ' ')); ?></td>
            </tr>
        </table>

        <div class="document-footer">
            Document généré le <?php echo e(now()->format('d/m/Y à H:i')); ?> — <?php echo e(config('app.name')); ?>

        </div>

    </div>

</body>
</html>
<?php /**PATH D:\ManageX\resources\views/pdf/payroll-civ.blade.php ENDPATH**/ ?>