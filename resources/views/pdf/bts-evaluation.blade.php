<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche BTS - {{ $intern->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; line-height: 1.4; color: #333; padding: 15px 25px; }

        .header-table { width: 100%; margin-bottom: 15px; }
        .header-left { text-align: left; font-size: 9px; line-height: 1.6; }
        .header-center { text-align: center; }
        .header-right { text-align: right; font-size: 9px; line-height: 1.6; font-weight: bold; }

        .doc-title { text-align: center; font-size: 14px; font-weight: bold; text-decoration: underline; margin: 15px 0; text-transform: uppercase; }

        .info-block { margin-bottom: 12px; }
        .info-line { margin-bottom: 4px; font-size: 11px; }
        .info-label { font-weight: bold; }
        .info-dotted { border-bottom: 1px dotted #999; display: inline-block; min-width: 200px; padding-bottom: 2px; }

        .eval-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        .eval-table th, .eval-table td { border: 1px solid #000; padding: 8px 10px; }
        .eval-table th { background-color: #f0f0f0; text-align: left; font-weight: bold; }
        .eval-table .score-col { text-align: center; width: 80px; font-weight: bold; font-size: 13px; }
        .eval-table .total-row { background-color: #e8e8e8; font-weight: bold; font-size: 12px; }

        .nota { font-size: 9px; margin: 10px 0; line-height: 1.6; }
        .nota strong { font-weight: bold; }

        .appreciation { margin: 15px 0; }
        .appreciation-title { font-weight: bold; font-size: 11px; margin-bottom: 5px; }
        .appreciation-lines { border-bottom: 1px dotted #666; height: 18px; margin-bottom: 3px; }

        .footer { margin-top: 30px; }
        .footer-table { width: 100%; }
        .signature-block { vertical-align: top; padding-top: 5px; }
        .signature-label { font-weight: bold; font-size: 10px; margin-bottom: 40px; }
    </style>
</head>
<body>

    {{-- En-tête officiel --}}
    <table class="header-table">
        <tr>
            <td style="width:40%;" class="header-left">
                MINISTERE DE L'EDUCATION SUPERIEURE<br>
                ET DE LA RECHERCHE SCIENTIFIQUE<br>
                -------------------------<br>
                DIRECTION DES EXAMENS, DES CONCOURS<br>
                ET DE L'ORIENTATION<br>
                TEL 20 32 90 95
            </td>
            <td style="width:20%;" class="header-center">
                <div style="font-size:10px; font-weight:bold; margin-top:30px;">MEPS<br>MINISTÈRE DE L'EMPLOI<br>ET DE LA PROTECTION</div>
            </td>
            <td style="width:40%;" class="header-right">
                REPUBLIQUE DE COTE D'IVOIRE<br><br>
                UNION - DISCIPLINE - TRAVAIL
            </td>
        </tr>
    </table>

    {{-- Titre --}}
    <div class="doc-title">FICHE D'ÉVALUATION DU STAGIAIRE POUR L'EXAMEN DU BTS</div>

    {{-- Informations --}}
    <div class="info-block">
        <div class="info-line"><span class="info-label">Nom et prénoms du stagiaire :</span> <span class="info-dotted">{{ $intern->name }}</span></div>
        <div class="info-line"><span class="info-label">N° du stagiaire :</span> <span class="info-dotted">{{ $evaluation->intern_bts_number ?? '___________' }}</span></div>
        <div class="info-line"><span class="info-label">N°BTS :</span> <span class="info-dotted">{{ $evaluation->intern_bts_number ?? '___________' }}</span> &nbsp;&nbsp;&nbsp; <span class="info-label">Filière :</span> <span class="info-dotted">{{ $evaluation->intern_field ?? '___________' }}</span></div>
        <div class="info-line"><span class="info-label">Nom de l'entreprise :</span> <span class="info-dotted">Ya Consulting</span></div>
        <div class="info-line"><span class="info-label">Adresse et contacts téléphoniques (entreprise) :</span> <span class="info-dotted">Abidjan, Côte d'Ivoire</span></div>
        <div class="info-line"><span class="info-label">Période stage (2 mois minimum) : Du</span> <span class="info-dotted">{{ $evaluation->stage_start_date->format('d/m/Y') }}</span> <span class="info-label">au</span> <span class="info-dotted">{{ $evaluation->stage_end_date->format('d/m/Y') }}</span></div>
        <div class="info-line"><span class="info-label">Nom et prénoms (maître de stage) :</span> <span class="info-dotted">{{ $evaluator->name }}</span></div>
        <div class="info-line"><span class="info-label">Fonction occupée dans l'entreprise (maître de stage) :</span> <span class="info-dotted">{{ $evaluator->position->name ?? 'Responsable' }}</span></div>
        <div class="info-line"><span class="info-label">Contact téléphoniques (maître de stage) :</span> <span class="info-dotted">{{ $evaluator->phone ?? '___________' }}</span></div>
    </div>

    {{-- Tableau d'évaluation --}}
    <table class="eval-table">
        <thead>
            <tr>
                <th>CRITÈRES D'ÉVALUATION</th>
                <th class="score-col">NOTES</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1/ Assiduité et ponctualité</td>
                <td class="score-col">{{ $evaluation->assiduity_score }} /3</td>
            </tr>
            <tr>
                <td>2/ Relation humaine et professionnelles</td>
                <td class="score-col">{{ $evaluation->relations_score }} /4</td>
            </tr>
            <tr>
                <td>3/ Intelligence d'exécution des tâches</td>
                <td class="score-col">{{ $evaluation->execution_score }} /6</td>
            </tr>
            <tr>
                <td>4/ Esprit d'initiative</td>
                <td class="score-col">{{ $evaluation->initiative_score }} /4</td>
            </tr>
            <tr>
                <td>5/ Présentation</td>
                <td class="score-col">{{ $evaluation->presentation_score }} /3</td>
            </tr>
            <tr class="total-row">
                <td>TOTAL (note définitive)</td>
                <td class="score-col">{{ $evaluation->total_score }} /20</td>
            </tr>
        </tbody>
    </table>

    {{-- Notes importantes --}}
    <div class="nota">
        <strong>N.B :</strong> toute note supérieure à 16/20 doit faire objet de rapport de justification sous peine d'invalidité par le jury.<br>
        - Les ratures, les surcharges ou l'absence de cachet de l'entreprise annulent le présent document<br>
        - Le stage ne peut être effectué dans un établissement de formation sous peine d'invalidité.
    </div>

    {{-- Appréciation --}}
    <div class="appreciation">
        <span class="appreciation-title">Appréciation du maître de stage :</span>
        @if($evaluation->appreciation)
            <p style="margin-top: 5px; font-style: italic;">{{ $evaluation->appreciation }}</p>
        @else
            <div class="appreciation-lines"></div>
            <div class="appreciation-lines"></div>
            <div class="appreciation-lines"></div>
        @endif
    </div>

    {{-- Justification si > 16 --}}
    @if($evaluation->total_score > 16 && $evaluation->justification_report)
    <div style="margin-top: 10px; padding: 8px; border: 1px solid #333; background: #fafafa;">
        <strong>Rapport justificatif (note > 16/20) :</strong>
        <p style="margin-top: 5px;">{{ $evaluation->justification_report }}</p>
    </div>
    @endif

    {{-- Signature --}}
    <table class="footer-table" style="margin-top: 25px;">
        <tr>
            <td class="signature-block" style="width: 50%;">
                <div class="signature-label">Date : {{ now()->format('d/m/Y') }}</div>
                <div style="margin-top: 30px;"><strong>Signature du maître de stage</strong></div>
            </td>
            <td class="signature-block" style="width: 50%; text-align: right;">
                <div style="margin-top: 30px;"><strong>(Cachet de l'entreprise) :</strong></div>
            </td>
        </tr>
    </table>

    <div style="margin-top: 20px; text-align: center; font-size: 8px; color: #999;">
        Document généré par ManageX le {{ now()->format('d/m/Y à H:i') }}
    </div>

</body>
</html>
