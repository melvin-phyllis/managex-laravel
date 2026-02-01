<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport de Progression - {{ $intern->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }
        .header {
            border-bottom: 3px solid #8b5cf6;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #8b5cf6;
        }
        .document-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
            margin: 15px 0;
            padding: 12px;
            background: linear-gradient(135deg, #f3e8ff 0%, #ede9fe 100%);
            border-radius: 6px;
        }
        .intern-info {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f9fafb;
            border-radius: 6px;
        }
        .intern-name {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
        }
        .intern-details {
            color: #6b7280;
            margin-top: 5px;
        }
        .period-info {
            float: right;
            text-align: right;
            font-size: 10px;
            color: #6b7280;
        }
        .summary-box {
            margin-bottom: 20px;
            padding: 15px;
            background: linear-gradient(135deg, #8b5cf6 0%, #a855f7 100%);
            color: white;
            border-radius: 8px;
        }
        .summary-grid {
            display: table;
            width: 100%;
        }
        .summary-item {
            display: table-cell;
            text-align: center;
            padding: 10px;
        }
        .summary-value {
            font-size: 24px;
            font-weight: bold;
        }
        .summary-label {
            font-size: 10px;
            opacity: 0.9;
        }
        .evaluations-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .evaluations-table th {
            background-color: #8b5cf6;
            color: white;
            padding: 10px;
            font-size: 10px;
            text-align: center;
        }
        .evaluations-table td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
        }
        .evaluations-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .grade-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 9px;
        }
        .grade-a { background-color: #d1fae5; color: #065f46; }
        .grade-b { background-color: #dbeafe; color: #1e40af; }
        .grade-c { background-color: #fef3c7; color: #92400e; }
        .grade-d { background-color: #ffedd5; color: #c2410c; }
        .grade-e { background-color: #fee2e2; color: #b91c1c; }
        .averages-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .averages-table td {
            padding: 10px;
            vertical-align: top;
        }
        .avg-box {
            padding: 12px;
            background-color: #f9fafb;
            border-radius: 6px;
            text-align: center;
        }
        .avg-box h4 {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 5px;
        }
        .avg-box .value {
            font-size: 18px;
            font-weight: bold;
            color: #8b5cf6;
        }
        .progress-bar {
            width: 100%;
            height: 6px;
            background-color: #e5e7eb;
            border-radius: 3px;
            margin-top: 5px;
        }
        .progress-fill {
            height: 100%;
            background-color: #8b5cf6;
            border-radius: 3px;
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
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">ManageX</div>
        <div style="color: #666; font-size: 12px;">Rapport de Progression - Stagiaire</div>
    </div>

    <div class="document-title">
        RAPPORT DE PROGRESSION
    </div>

    <div class="intern-info" style="overflow: hidden;">
        <div class="period-info">
            @if($dateFrom && $dateTo)
                Période : {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}
            @else
                Toutes les évaluations
            @endif
            <br>
            Généré le {{ now()->format('d/m/Y') }}
        </div>
        <div class="intern-name">{{ $intern->name }}</div>
        <div class="intern-details">
            {{ $intern->email }} | {{ $intern->department->name ?? 'Non assigné' }}
            @if($intern->supervisor)
                | Tuteur: {{ $intern->supervisor->name }}
            @endif
        </div>
    </div>

    @if($evaluations->isNotEmpty())
        <!-- Summary -->
        <div class="summary-box">
            <table class="summary-grid" style="width: 100%;">
                <tr>
                    <td class="summary-item">
                        <div class="summary-value">{{ $evaluations->count() }}</div>
                        <div class="summary-label">Évaluations</div>
                    </td>
                    <td class="summary-item">
                        <div class="summary-value">{{ $averages['total'] }}/10</div>
                        <div class="summary-label">Moyenne Générale</div>
                    </td>
                    <td class="summary-item">
                        @php
                            $avgGrade = match(true) {
                                $averages['total'] >= 9 => 'A',
                                $averages['total'] >= 7 => 'B',
                                $averages['total'] >= 5 => 'C',
                                $averages['total'] >= 3 => 'D',
                                default => 'E',
                            };
                        @endphp
                        <div class="summary-value">{{ $avgGrade }}</div>
                        <div class="summary-label">Grade Moyen</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Averages by Criteria -->
        <table class="averages-table">
            <tr>
                <td width="25%">
                    <div class="avg-box">
                        <h4>Discipline</h4>
                        <div class="value">{{ $averages['discipline'] }}/2.5</div>
                        <div class="progress-bar"><div class="progress-fill" style="width: {{ ($averages['discipline'] / 2.5) * 100 }}%"></div></div>
                    </div>
                </td>
                <td width="25%">
                    <div class="avg-box">
                        <h4>Comportement</h4>
                        <div class="value">{{ $averages['behavior'] }}/2.5</div>
                        <div class="progress-bar"><div class="progress-fill" style="width: {{ ($averages['behavior'] / 2.5) * 100 }}%"></div></div>
                    </div>
                </td>
                <td width="25%">
                    <div class="avg-box">
                        <h4>Compétences</h4>
                        <div class="value">{{ $averages['skills'] }}/2.5</div>
                        <div class="progress-bar"><div class="progress-fill" style="width: {{ ($averages['skills'] / 2.5) * 100 }}%"></div></div>
                    </div>
                </td>
                <td width="25%">
                    <div class="avg-box">
                        <h4>Communication</h4>
                        <div class="value">{{ $averages['communication'] }}/2.5</div>
                        <div class="progress-bar"><div class="progress-fill" style="width: {{ ($averages['communication'] / 2.5) * 100 }}%"></div></div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Evaluations History -->
        <h3 style="font-size: 12px; margin-bottom: 10px; color: #374151;">Historique des Évaluations</h3>
        <table class="evaluations-table">
            <thead>
                <tr>
                    <th>Semaine</th>
                    <th>Discipline</th>
                    <th>Comportement</th>
                    <th>Compétences</th>
                    <th>Communication</th>
                    <th>Total</th>
                    <th>Grade</th>
                </tr>
            </thead>
            <tbody>
                @foreach($evaluations as $eval)
                    <tr>
                        <td style="text-align: left;">{{ $eval->week_start->format('d/m/Y') }}</td>
                        <td>{{ $eval->discipline_score }}</td>
                        <td>{{ $eval->behavior_score }}</td>
                        <td>{{ $eval->skills_score }}</td>
                        <td>{{ $eval->communication_score }}</td>
                        <td><strong>{{ $eval->total_score }}</strong></td>
                        <td>
                            <span class="grade-badge grade-{{ strtolower($eval->grade_letter) }}">{{ $eval->grade_letter }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="text-align: center; padding: 40px; color: #6b7280;">
            Aucune évaluation disponible pour cette période.
        </div>
    @endif

    <div class="footer">
        <p>Document généré automatiquement par ManageX le {{ now()->format('d/m/Y à H:i') }}</p>
        <p>Ce document est confidentiel et destiné uniquement aux personnes autorisées.</p>
    </div>
</body>
</html>
