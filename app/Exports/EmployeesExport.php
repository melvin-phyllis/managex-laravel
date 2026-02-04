<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeesExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    protected ?string $departmentId = null;

    protected ?string $status = null;

    protected ?string $contractType = null;

    public function __construct(?string $departmentId = null, ?string $status = null, ?string $contractType = null)
    {
        $this->departmentId = $departmentId;
        $this->status = $status;
        $this->contractType = $contractType;
    }

    public function query(): Builder
    {
        $query = User::where('role', 'employee')
            ->with(['department', 'position', 'supervisor']);

        if ($this->departmentId) {
            $query->where('department_id', $this->departmentId);
        }
        if ($this->status) {
            $query->where('status', $this->status);
        }
        if ($this->contractType) {
            $query->where('contract_type', $this->contractType);
        }

        return $query->orderBy('name');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Matricule',
            'Nom',
            'Email',
            'Telephone',
            'Departement',
            'Poste',
            'Type Contrat',
            'Date Embauche',
            'Date Fin Contrat',
            'Statut',
            'Date Naissance',
            'Genre',
            'Adresse',
            'Ville',
            'Code Postal',
            'Pays',
            'Contact Urgence',
            'Tel Urgence',
            'Situation Familiale',
            'Nb Enfants',
            'Nb Parts',
            'CNPS',
            'Salaire Base',
            'IBAN',
            'Conges Solde',
            'Maladie Solde',
            'RTT Solde',
            'Superviseur',
            'Notes',
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->employee_id ?? '-',
            $user->name,
            $user->email,
            $user->telephone ?? '-',
            $user->department->name ?? '-',
            $user->position->name ?? ($user->poste ?? '-'),
            strtoupper($user->contract_type ?? '-'),
            $user->hire_date?->format('d/m/Y') ?? '-',
            $user->contract_end_date?->format('d/m/Y') ?? '-',
            $user->status ?? 'actif',
            $user->date_of_birth?->format('d/m/Y') ?? '-',
            $user->gender ?? '-',
            $user->address ?? '-',
            $user->city ?? '-',
            $user->postal_code ?? '-',
            $user->country ?? '-',
            $user->emergency_contact_name ?? '-',
            $user->emergency_contact_phone ?? '-',
            $user->marital_status ?? '-',
            $user->children_count ?? '-',
            $user->number_of_parts ?? '-',
            $user->cnps_number ?? '-',
            $user->base_salary ? number_format($user->base_salary, 0, ',', ' ') : '-',
            $user->bank_iban ?? '-',
            $user->leave_balance ?? '-',
            $user->sick_leave_balance ?? '-',
            $user->rtt_balance ?? '-',
            $user->supervisor->name ?? '-',
            $user->notes ?? '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'color' => ['rgb' => '4F46E5']],
            ],
        ];
    }
}
