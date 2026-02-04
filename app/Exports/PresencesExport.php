<?php

namespace App\Exports;

use App\Models\Presence;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PresencesExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query(): Builder
    {
        $query = Presence::query()->with('user');

        if (! empty($this->filters['user_id'])) {
            $query->where('user_id', $this->filters['user_id']);
        }

        if (! empty($this->filters['date_debut'])) {
            $query->where('date', '>=', $this->filters['date_debut']);
        }

        if (! empty($this->filters['date_fin'])) {
            $query->where('date', '<=', $this->filters['date_fin']);
        }

        return $query->orderBy('date', 'desc')->orderBy('user_id');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Employé',
            'Email',
            'Poste',
            'Date',
            'Heure d\'arrivée',
            'Heure de départ',
            'Durée',
            'Notes',
        ];
    }

    /**
     * @param  Presence  $presence
     */
    public function map($presence): array
    {
        return [
            $presence->id,
            $presence->user->name,
            $presence->user->email,
            $presence->user->poste ?? '-',
            $presence->date->format('d/m/Y'),
            $presence->check_in->format('H:i'),
            $presence->check_out ? $presence->check_out->format('H:i') : '-',
            $presence->duree ?? '-',
            $presence->notes ?? '',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Style pour la première ligne (en-têtes)
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2563EB'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }
}
