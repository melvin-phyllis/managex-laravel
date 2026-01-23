<?php

namespace Database\Seeders;

use App\Services\Messaging\MessagingService;
use Illuminate\Database\Seeder;

class MessagingSeeder extends Seeder
{
    public function run(): void
    {
        $service = new MessagingService();
        
        // Create default channels
        $this->command->info('Creating default channels...');
        $channels = $service->createDefaultChannels();
        $this->command->info("Created: general, annonces");
        
        // Create department channels
        $this->command->info('Creating department channels...');
        $deptChannels = $service->createDepartmentChannels();
        $this->command->info("Created " . count($deptChannels) . " department channels");
        
        // Add all users to general and announcements
        $this->command->info('Adding users to default channels...');
        $users = \App\Models\User::all();
        
        $generalChannel = $channels['general'];
        $annoncesChannel = $channels['annonces'];
        
        foreach ($users as $user) {
            // Add to general
            $generalChannel->participants()->firstOrCreate(
                ['user_id' => $user->id],
                ['role' => 'member', 'joined_at' => now()]
            );
            
            // Add to announcements
            $annoncesChannel->participants()->firstOrCreate(
                ['user_id' => $user->id],
                ['role' => $user->isAdmin() ? 'admin' : 'member', 'joined_at' => now()]
            );
        }
        
        $this->command->info('Messaging channels setup complete!');
    }
}
