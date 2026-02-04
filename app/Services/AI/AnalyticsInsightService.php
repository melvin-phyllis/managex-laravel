<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AnalyticsInsightService
{
    protected MistralService $mistral;

    public function __construct(MistralService $mistral)
    {
        $this->mistral = $mistral;
    }

    /**
     * Générer des insights IA à partir des KPIs et graphiques.
     *
     * @param  array  $kpis  Données KPI (effectif, présences, turnover, etc.)
     * @param  array  $filters  Filtres appliqués (period, department_id, etc.)
     * @return string|null Insights en markdown ou null si indisponible
     */
    public function generateInsights(array $kpis, array $filters = []): ?string
    {
        if (! $this->mistral->isAvailable()) {
            return null;
        }

        $cacheKey = 'ai.analytics_insights.'.md5(json_encode($kpis).json_encode($filters));
        $cacheTtl = config('ai.cache_ttl', 900);

        return Cache::remember($cacheKey, $cacheTtl, function () use ($kpis) {
            return $this->callMistral($kpis);
        });
    }

    /**
     * Invalider le cache des insights.
     */
    public static function clearCache(): void
    {
        // On ne peut pas supprimer par préfixe facilement avec le driver database,
        // mais on peut invalider la clé spécifique si on la connaît.
        // En pratique, le TTL de 15 min suffit.
    }

    /**
     * Appeler Mistral pour générer les insights.
     */
    protected function callMistral(array $kpis): ?string
    {
        $dataForPrompt = $this->formatKpisForPrompt($kpis);

        $system = <<<'PROMPT'
Tu es un analyste RH senior. On te fournit les KPIs d'une entreprise pour une période donnée.

INSTRUCTIONS:
- Génère exactement 3 à 5 insights courts et actionnables en français
- Chaque insight doit faire 1-2 lignes maximum
- Utilise des indicateurs de tendance: hausse/baisse/stable
- Identifie les anomalies et risques
- Propose une recommandation concrète quand pertinent
- Format: liste à puces avec des mots-clés en gras (**mot**)
- Ne répète pas les chiffres bruts, interprète-les
- Sois direct et factuel, pas de formules de politesse
PROMPT;

        $response = $this->mistral->chat($system, [
            ['role' => 'user', 'content' => "Voici les KPIs RH actuels:\n\n{$dataForPrompt}\n\nGénère les insights."],
        ]);

        if ($response === null) {
            Log::warning('AnalyticsInsightService: impossible de générer les insights');
        }

        return $response;
    }

    /**
     * Formater les KPIs en texte lisible pour le prompt.
     */
    protected function formatKpisForPrompt(array $kpis): string
    {
        $lines = [];

        if (isset($kpis['effectif_total'])) {
            $lines[] = "Effectif total: {$kpis['effectif_total']['value']} (variation: {$kpis['effectif_total']['variation']}%)";
        }

        if (isset($kpis['presents_today'])) {
            $lines[] = "Présents aujourd'hui: {$kpis['presents_today']['value']}/{$kpis['presents_today']['expected']} ({$kpis['presents_today']['percentage']}%)";
        }

        if (isset($kpis['en_conge'])) {
            $types = $kpis['en_conge']['types'] ?? [];
            $detail = collect($types)->map(fn ($v, $k) => "{$k}: {$v}")->implode(', ');
            $lines[] = "En congé: {$kpis['en_conge']['value']}".($detail ? " ({$detail})" : '');
        }

        if (isset($kpis['absents_non_justifies'])) {
            $lines[] = "Absents non justifiés: {$kpis['absents_non_justifies']['value']}";
        }

        if (isset($kpis['masse_salariale'])) {
            $lines[] = "Masse salariale: {$kpis['masse_salariale']['formatted']} (variation: {$kpis['masse_salariale']['variation']}%)";
        }

        if (isset($kpis['heures_supplementaires'])) {
            $lines[] = "Heures supplémentaires: {$kpis['heures_supplementaires']['value']}h ({$kpis['heures_supplementaires']['count']} employés)";
        }

        if (isset($kpis['turnover'])) {
            $lines[] = "Turnover: {$kpis['turnover']['rate']}% (entrées: {$kpis['turnover']['entries']}, sorties: {$kpis['turnover']['exits']})";
        }

        if (isset($kpis['tasks'])) {
            $lines[] = "Tâches: {$kpis['tasks']['completed']} complétées / {$kpis['tasks']['total']} total ({$kpis['tasks']['pending']} en attente)";
        }

        if (isset($kpis['late_hours'])) {
            $lines[] = "Retards cumulés: {$kpis['late_hours']['total']}h ({$kpis['late_hours']['employees']} employés)";
        }

        if (isset($kpis['interns'])) {
            $lines[] = "Stagiaires: {$kpis['interns']['count']} actifs, {$kpis['interns']['to_evaluate']} à évaluer";
        }

        return implode("\n", $lines);
    }
}
