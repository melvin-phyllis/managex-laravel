<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use App\Notifications\WelcomeEmployeeNotification;
use App\Models\User;
use Tests\TestCase;

class MailTest extends TestCase
{
    /** @test */
    public function it_sends_welcome_email()
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        // Le mot de passe n'est plus envoyé - utilisation d'un lien de réinitialisation sécurisé
        $user->notify(new WelcomeEmployeeNotification($user->name));

        Mail::assertSent(function (\Illuminate\Mail\Mailable $mail) use ($user) {
            return in_array($user->email, array_map(fn($to) => $to['address'], $mail->to ?? []));
        });
    }
}
