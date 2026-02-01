<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #4F46E5;
        }
        .header h1 {
            color: #4F46E5;
            font-size: 24px;
            margin: 0 0 10px 0;
        }
        .header .subtitle {
            color: #666;
            font-size: 14px;
        }
        .meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 10px;
            background: #f3f4f6;
            border-radius: 8px;
        }
        .meta span {
            font-size: 11px;
            color: #666;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #4F46E5;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
        }
        .kpi-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .kpi-row {
            display: table-row;
        }
        .kpi-card {
            display: table-cell;
            width: 25%;
            padding: 10px;
            text-align: center;
            border: 1px solid #e5e7eb;
            background: #fafafa;
        }
        .kpi-value {
            font-size: 24px;
            font-weight: bold;
            color: #111;
        }
        .kpi-label {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 8px 10px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background: #4F46E5;
            color: white;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background: #f9fafb;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <div class="subtitle">
            Période : {{ $period_label }}
            @if($department)
                | Département : {{ $department->name }}
            @endif
        </div>
    </div>

    <div class="meta">
        <span>Généré le : {{ $generated_at }}</span>
        <span>ManageX - Système de gestion RH</span>
    </div>

    {{-- KPIs Principaux --}}
    <div class="section">
        <div class="section-title">Indicateurs Clés de Performance (KPIs)</div>
        
        <div class="kpi-grid">
            <div class="kpi-row">
                <div class="kpi-card">
                    <div class="kpi-value">{{ $kpis['effectif_total']['value'] ?? 0 }}</div>
                    <div class="kpi-label">Effectif Total</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-value">{{ $kpis['presents_today']['percentage'] ?? 0 }}%</div>
                    <div class="kpi-label">Taux de Présence</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-value">{{ $kpis['turnover']['rate'] ?? 0 }}%</div>
                    <div class="kpi-label">Taux de Turnover</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-value">{{ $kpis['masse_salariale']['formatted'] ?? '0 FCFA' }}</div>
                    <div class="kpi-label">Masse Salariale</div>
                </div>
            </div>
        </div>

        <table>
            <tr>
                <td><strong>En congé</strong></td>
                <td>{{ $kpis['en_conge']['value'] ?? 0 }} employé(s)</td>
                <td><strong>Absents non justifiés</strong></td>
                <td>{{ $kpis['absents_non_justifies']['value'] ?? 0 }}</td>
            </tr>
            <tr>
                <td><strong>Heures supplémentaires</strong></td>
                <td>{{ $kpis['heures_supplementaires']['value'] ?? 0 }}h</td>
                <td><strong>Tâches complétées</strong></td>
                <td>{{ $kpis['tasks']['completed'] ?? 0 }}</td>
            </tr>
            <tr>
                <td><strong>Stagiaires actifs</strong></td>
                <td>{{ $kpis['interns']['count'] ?? 0 }}</td>
                <td><strong>Heures de retard</strong></td>
                <td>{{ $kpis['late_hours']['total'] ?? 0 }}h</td>
            </tr>
        </table>
    </div>

    {{-- Répartition par département --}}
    <div class="section">
        <div class="section-title">Effectif par Département</div>
        <table>
            <thead>
                <tr>
                    <th>Département</th>
                    <th>Nombre d'employés</th>
                </tr>
            </thead>
            <tbody>
                @foreach($department_stats as $dept)
                <tr>
                    <td>{{ $dept['name'] }}</td>
                    <td>{{ $dept['count'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Top Retardataires --}}
    @if(count($latecomers) > 0)
    <div class="section">
        <div class="section-title">Top Retardataires du Mois</div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Employé</th>
                    <th>Département</th>
                    <th>Nombre de retards</th>
                    <th>Moyenne (min)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($latecomers as $latecomer)
                <tr>
                    <td>{{ $latecomer['rank'] }}</td>
                    <td>{{ $latecomer['name'] }}</td>
                    <td>{{ $latecomer['department'] }}</td>
                    <td>{{ $latecomer['count'] }}</td>
                    <td>{{ $latecomer['avg_minutes'] }} min</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Congés en attente --}}
    @if($pending_leaves->count() > 0)
    <div class="section">
        <div class="section-title">Demandes de Congés en Attente</div>
        <table>
            <thead>
                <tr>
                    <th>Employé</th>
                    <th>Type</th>
                    <th>Du</th>
                    <th>Au</th>
                    <th>Durée</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pending_leaves as $leave)
                <tr>
                    <td>{{ $leave->user->name }}</td>
                    <td>{{ $leave->type_label }}</td>
                    <td>{{ $leave->date_debut->format('d/m/Y') }}</td>
                    <td>{{ $leave->date_fin->format('d/m/Y') }}</td>
                    <td>{{ $leave->duree }} jour(s)</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Top Employés (Évaluations) --}}
    @if(!empty($top_performers['employees']))
    <div class="section">
        <div class="section-title">🏆 Top Employés (Évaluations)</div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Employé</th>
                    <th>Département</th>
                    <th>Note</th>
                    <th>%</th>
                </tr>
            </thead>
            <tbody>
                @foreach($top_performers['employees'] as $emp)
                <tr>
                    <td>{{ $emp['rank'] }}</td>
                    <td>{{ $emp['name'] }}</td>
                    <td>{{ $emp['department'] }}</td>
                    <td>{{ $emp['score'] }}/{{ $emp['max_score'] }}</td>
                    <td><span class="badge badge-success">{{ $emp['percentage'] }}%</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Top Stagiaires (Évaluations) --}}
    @if(!empty($top_performers['interns']))
    <div class="section">
        <div class="section-title">⭐ Top Stagiaires (Évaluations)</div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Stagiaire</th>
                    <th>Département</th>
                    <th>Note Moyenne</th>
                    <th>%</th>
                </tr>
            </thead>
            <tbody>
                @foreach($top_performers['interns'] as $intern)
                <tr>
                    <td>{{ $intern['rank'] }}</td>
                    <td>{{ $intern['name'] }}</td>
                    <td>{{ $intern['department'] }}</td>
                    <td>{{ $intern['score'] }}/{{ $intern['max_score'] }}</td>
                    <td><span class="badge badge-success">{{ $intern['percentage'] }}%</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Meilleure Assiduité --}}
    @if(!empty($best_attendance))
    <div class="section">
        <div class="section-title">👏 Meilleure Assiduité</div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Employé</th>
                    <th>Département</th>
                    <th>Présences</th>
                    <th>Ponctualité</th>
                </tr>
            </thead>
            <tbody>
                @foreach($best_attendance as $att)
                <tr>
                    <td>{{ $att['rank'] }}</td>
                    <td>{{ $att['name'] }}</td>
                    <td>{{ $att['department'] }}</td>
                    <td>{{ $att['presence_count'] }} jours</td>
                    <td><span class="badge badge-success">{{ $att['punctuality_rate'] }}%</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Résumé Évaluations --}}
    @if(!empty($evaluation_stats))
    <div class="section">
        <div class="section-title">Résumé des Évaluations</div>
        <table>
            <tr>
                <td colspan="2" style="background: #10B981; color: white; font-weight: bold;">Employés</td>
                <td colspan="2" style="background: #8B5CF6; color: white; font-weight: bold;">Stagiaires</td>
            </tr>
            <tr>
                <td><strong>Évaluations validées</strong></td>
                <td>{{ $evaluation_stats['employees']['validated'] ?? 0 }}</td>
                <td><strong>Total évaluations</strong></td>
                <td>{{ $evaluation_stats['interns']['total_evaluations'] ?? 0 }}</td>
            </tr>
            <tr>
                <td><strong>Non évalués</strong></td>
                <td>{{ $evaluation_stats['employees']['not_evaluated'] ?? 0 }}</td>
                <td><strong>Note moyenne</strong></td>
                <td>{{ $evaluation_stats['interns']['avg_score'] ?? 0 }}/10</td>
            </tr>
            <tr>
                <td><strong>Note moyenne</strong></td>
                <td>{{ $evaluation_stats['employees']['avg_score'] ?? 0 }}/5.5</td>
                <td><strong>À évaluer cette semaine</strong></td>
                <td>{{ $evaluation_stats['interns']['not_evaluated_this_week'] ?? 0 }}</td>
            </tr>
        </table>
    </div>
    @endif

    <div class="footer">
        Ce rapport a été généré automatiquement par ManageX.<br>
        Pour toute question, contactez l'administrateur RH.
    </div>
</body>
</html>
