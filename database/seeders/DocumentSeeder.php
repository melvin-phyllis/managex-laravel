<?php

namespace Database\Seeders;

use App\Models\DocumentCategory;
use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        // ===============================================
        // Documents Contractuels - Gérés par Admin RH
        // ===============================================
        $contractual = DocumentCategory::create([
            'name' => 'Documents Contractuels',
            'slug' => 'contractual',
            'description' => 'Contrats et documents officiels de l\'entreprise',
            'icon' => '📝',
            'owner_type' => 'company',
            'requires_validation' => false,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $contractTypes = [
            [
                'name' => 'Contrat de travail',
                'slug' => 'work_contract',
                'description' => 'Votre contrat de travail signé (CDD, CDI, Stage)',
                'is_required' => true,
                'employee_can_upload' => false, // Admin only
                'employee_can_view' => true,
                'employee_can_delete' => false,
                'requires_validation' => false,
                'has_expiry_date' => false,
                'is_unique' => true,
                'allowed_extensions' => ['pdf'],
                'max_size_mb' => 10,
                'sort_order' => 1,
            ],
            [
                'name' => 'Règlement intérieur',
                'slug' => 'internal_rules',
                'description' => 'Règlement de l\'entreprise à lire et approuver',
                'is_required' => true,
                'employee_can_upload' => false,
                'employee_can_view' => true,
                'employee_can_delete' => false,
                'requires_validation' => false,
                'has_expiry_date' => false,
                'is_unique' => true,
                'allowed_extensions' => ['pdf'],
                'max_size_mb' => 10,
                'sort_order' => 2,
            ],
            [
                'name' => 'CV',
                'slug' => 'cv',
                'description' => 'Votre Curriculum Vitae',
                'is_required' => true,
                'employee_can_upload' => true, // Employé uploade son CV
                'employee_can_view' => true,
                'employee_can_delete' => true,
                'requires_validation' => false,
                'has_expiry_date' => false,
                'is_unique' => true,
                'allowed_extensions' => ['pdf'],
                'max_size_mb' => 5,
                'sort_order' => 3,
            ],
            [
                'name' => 'Avenant au contrat',
                'slug' => 'contract_amendment',
                'description' => 'Modifications de contrat (changement de poste, salaire, etc.)',
                'is_required' => false,
                'employee_can_upload' => false,
                'employee_can_view' => true,
                'employee_can_delete' => false,
                'requires_validation' => false,
                'has_expiry_date' => false,
                'is_unique' => false, // Plusieurs avenants possibles
                'allowed_extensions' => ['pdf'],
                'max_size_mb' => 5,
                'sort_order' => 4,
            ],
        ];

        foreach ($contractTypes as $type) {
            DocumentType::create(array_merge($type, ['category_id' => $contractual->id]));
        }

        $this->command->info('✅ Document types seeded successfully!');
    }
}
