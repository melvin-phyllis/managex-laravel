@props(['status', 'type' => 'default'])

@php
$colorMap = match($status) {
    'pending' => ['bg' => 'rgba(231, 227, 212, 0.5)', 'text' => '#8590AA'], // Attente: Beige/Gray
    'approved' => ['bg' => 'rgba(59, 139, 235, 0.15)', 'text' => '#3B8BEB'], // En cours: Blue
    'rejected' => ['bg' => 'rgba(178, 56, 80, 0.15)', 'text' => '#B23850'], // Rejeté: Red
    'completed' => ['bg' => 'rgba(196, 219, 246, 0.5)', 'text' => '#3B8BEB'], // Terminé: Light Blue/Blue
    'validated' => ['bg' => 'rgba(59, 139, 235, 0.2)', 'text' => '#3B8BEB'], // Validé: Blue
    'paid' => ['bg' => 'rgba(59, 139, 235, 0.2)', 'text' => '#3B8BEB'], // Payé: Blue
    'active' => ['bg' => 'rgba(59, 139, 235, 0.15)', 'text' => '#3B8BEB'], // Actif: Blue
    'inactive' => ['bg' => 'rgba(133, 144, 170, 0.1)', 'text' => '#8590AA'], // Inactif: Gray
    'low' => ['bg' => 'rgba(133, 144, 170, 0.1)', 'text' => '#8590AA'], // Basse: Gray
    'medium' => ['bg' => 'rgba(59, 139, 235, 0.15)', 'text' => '#3B8BEB'], // Moyenne: Blue
    'high' => ['bg' => 'rgba(178, 56, 80, 0.15)', 'text' => '#B23850'], // Haute: Red
    'conge' => ['bg' => 'rgba(59, 139, 235, 0.15)', 'text' => '#3B8BEB'], // Congé: Blue
    'maladie' => ['bg' => 'rgba(178, 56, 80, 0.15)', 'text' => '#B23850'], // Maladie: Red
    'autre' => ['bg' => 'rgba(133, 144, 170, 0.1)', 'text' => '#8590AA'], // Autre: Gray
    default => ['bg' => 'rgba(133, 144, 170, 0.1)', 'text' => '#8590AA'], // Default: Gray
};


$labels = match($status) {
    'pending' => 'En attente',
    'approved' => 'En cours',
    'rejected' => 'Refusé',
    'completed' => 'Terminé (à valider)',
    'validated' => 'Validé',
    'paid' => 'Payé',
    'active' => 'Actif',
    'inactive' => 'Inactif',
    'low' => 'Basse',
    'medium' => 'Moyenne',
    'high' => 'Haute',
    'conge' => 'Congé',
    'maladie' => 'Maladie',
    'autre' => 'Autre',
    default => $status,
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"]) }}
      style="background-color: {{ $colorMap['bg'] }}; color: {{ $colorMap['text'] }};">
    {{ $slot->isEmpty() ? $labels : $slot }}
</span>

