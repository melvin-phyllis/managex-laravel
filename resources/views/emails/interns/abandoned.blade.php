<x-mail::message>
# Notification d'abandon de stage

Bonjour {{ $intern->name }},

Le système de gestion ManageX a détecté une absence prolongée de votre part.

En effet, nous n'avons enregistré aucun pointage de présence ni de demande de congé validée pour votre compte au cours des **{{ $daysOfAbsence }} derniers jours**.

Conformément au règlement intérieur concernant les stages, une absence injustifiée dépassant ce délai est considérée comme un **abandon de poste**.

**Statut actuel :** Abandonné
**Date du constat :** {{ now()->format('d/m/Y') }}

Si vous pensez qu'il s'agit d'une erreur ou si vous avez des justificatifs à fournir, nous vous prions de contacter le département des Ressources Humaines dans les plus brefs délais.

Merci,<br>
L'administration {{ config('app.name') }}
</x-mail::message>
