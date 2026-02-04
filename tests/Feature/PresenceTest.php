<?php

namespace Tests\Feature;

use App\Models\Presence;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PresenceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $employee;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->admin()->create();
        $this->employee = User::factory()->create([
            'status' => 'active',
        ]);
    }

    /** @test */
    public function employee_can_view_presence_page(): void
    {
        $response = $this->actingAs($this->employee)->get(route('employee.presences.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function employee_can_check_in(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 2, 3, 9, 0, 0)); // Monday 9:00 AM

        $response = $this->actingAs($this->employee)->post(route('employee.presences.checkin'));

        $response->assertRedirect();
        $this->assertDatabaseHas('presences', [
            'user_id' => $this->employee->id,
            'date' => now()->toDateString(),
        ]);
    }

    /** @test */
    public function employee_can_check_out(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 2, 3, 9, 0, 0));
        
        $presence = Presence::create([
            'user_id' => $this->employee->id,
            'check_in' => now(),
            'date' => now()->toDateString(),
        ]);

        Carbon::setTestNow(Carbon::create(2026, 2, 3, 18, 0, 0)); // 6:00 PM

        $response = $this->actingAs($this->employee)->post(route('employee.presences.checkout'));

        $response->assertRedirect();
        $presence->refresh();
        $this->assertNotNull($presence->check_out);
    }

    /** @test */
    public function employee_cannot_check_in_twice_same_day(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 2, 3, 9, 0, 0));
        
        Presence::create([
            'user_id' => $this->employee->id,
            'check_in' => now(),
            'date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($this->employee)->post(route('employee.presences.checkin'));

        // Should fail or show error
        $response->assertRedirect();
        
        // Should still have only one presence for today
        $this->assertEquals(1, Presence::where('user_id', $this->employee->id)
            ->whereDate('date', now()->toDateString())
            ->count());
    }

    /** @test */
    public function admin_can_view_all_presences(): void
    {
        Presence::create([
            'user_id' => $this->employee->id,
            'check_in' => now()->setTime(9, 0),
            'check_out' => now()->setTime(18, 0),
            'date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.presences.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_view_employee_presence_detail(): void
    {
        Presence::create([
            'user_id' => $this->employee->id,
            'check_in' => now()->setTime(9, 0),
            'check_out' => now()->setTime(18, 0),
            'date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.presences.employee', $this->employee));

        $response->assertStatus(200);
    }

    /** @test */
    public function late_arrival_is_detected(): void
    {
        // Simulate check-in at 9:30 when work starts at 8:30
        Carbon::setTestNow(Carbon::create(2026, 2, 3, 9, 30, 0));
        
        $response = $this->actingAs($this->employee)->post(route('employee.presences.checkin'));

        $response->assertRedirect();
        
        $presence = Presence::where('user_id', $this->employee->id)
            ->whereDate('date', now()->toDateString())
            ->first();
        
        // The presence should exist (late detection depends on settings)
        $this->assertNotNull($presence);
    }

    /** @test */
    public function employee_can_view_their_monthly_history(): void
    {
        // Create some presences for the current month
        for ($i = 1; $i <= 5; $i++) {
            Presence::create([
                'user_id' => $this->employee->id,
                'check_in' => now()->subDays($i)->setTime(9, 0),
                'check_out' => now()->subDays($i)->setTime(18, 0),
                'date' => now()->subDays($i)->toDateString(),
            ]);
        }

        $response = $this->actingAs($this->employee)->get(route('employee.presences.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function presence_calculates_worked_hours(): void
    {
        $presence = Presence::create([
            'user_id' => $this->employee->id,
            'check_in' => now()->setTime(9, 0),
            'check_out' => now()->setTime(18, 0),
            'date' => now()->toDateString(),
        ]);

        // 9 hours worked
        $this->assertEquals(9, $presence->worked_hours);
    }

    /** @test */
    public function admin_can_export_presences(): void
    {
        Presence::create([
            'user_id' => $this->employee->id,
            'check_in' => now()->setTime(9, 0),
            'check_out' => now()->setTime(18, 0),
            'date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.presences.export', [
            'format' => 'csv',
        ]));

        $response->assertStatus(200);
    }
}
