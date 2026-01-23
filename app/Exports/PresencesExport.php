<?php

namespace App\Exports;

use App\Models\Presence;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Database\Eloquent\Builder;

class PresencesExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * @return Builder
     */
    public function query(): Builder
    {
        $query = Presence::query()->with('user');

        if (!empty($this->filters['user_id'])) {
            $query->where('user_id', $this->filters['user_id']);
        }

        if (!empty($this->filters['date_debut'])) {
            $query->where('date', '>=', $this->filters['date_debut']);
        }

        if (!empty($this->filters['date_fin'])) {
            $query->where('date', '<=', $this->filters['date_fin']);
        }

        return $query->orderBy('date', 'desc')->orderBy('user_id');
    }

    /**
     * @return array
     */
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
     * @param Presence $presence
     * @return array
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

    /**
     * @param Worksheet $sheet
     * @return array
     */
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
