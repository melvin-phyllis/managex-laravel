<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\WelcomeEmployeeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_sends_welcome_notification()
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        $user->notify(new WelcomeEmployeeNotification($user->name));

        Notification::assertSentTo($user, WelcomeEmployeeNotification::class);
    }
}
