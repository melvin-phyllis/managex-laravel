<?php

namespace App\Console\Commands;

use App\Services\Messaging\MessagingService;
use Illuminate\Console\Command;

class SetupMessagingChannels extends Command
{
    protected $signature = 'messaging:setup {--fresh : Remove all existing channels first}';

    protected $description = 'Setup default messaging channels (general, announcements, departments)';

    public function handle(): int
    {
        $service = new MessagingService;

        if ($this->option('fresh')) {
            $this->warn('Removing existing system channels...');
            \App\Models\Messaging\Conversation::whereIn('type', ['channel', 'announcement'])->delete();
        }

        // Create default channels
        $this->info('Creating default channels...');
        $channels = $service->createDefaultChannels();
        $this->line('  ✓ general - Discussions générales');
        $this->line('  ✓ annonces - Communications officielles RH');

        // Create department channels
        $this->info('Creating department channels...');
        $deptChannels = $service->createDepartmentChannels();
        foreach ($deptChannels as $deptId => $channel) {
            $this->line("  ✓ {$channel->name}");
        }

        // Add all users to default channels
        $this->info('Adding users to default channels...');
        $users = \App\Models\User::all();
        $generalChannel = $channels['general'];
        $annoncesChannel = $channels['annonces'];

        $bar = $this->output->createProgressBar(count($users));
        $bar->start();

        foreach ($users as $user) {
            $generalChannel->participants()->firstOrCreate(
                ['user_id' => $user->id],
                ['role' => 'member', 'joined_at' => now()]
            );

            $annoncesChannel->participants()->firstOrCreate(
                ['user_id' => $user->id],
                ['role' => $user->isAdmin() ? 'admin' : 'member', 'joined_at' => now()]
            );

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info('✅ Messaging channels setup complete!');
        $this->table(
            ['Type', 'Count'],
            [
                ['Default Channels', 2],
                ['Department Channels', count($deptChannels)],
                ['Users Added', count($users)],
            ]
        );

        return Command::SUCCESS;
    }
}
