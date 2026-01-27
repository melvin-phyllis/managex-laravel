<?php

namespace App\Observers;

use App\Models\Leave;
use App\Models\User;
use App\Notifications\LeaveRequestNotification;
use App\Notifications\LeaveStatusNotification;

class LeaveObserver
{
    /**
     * Handle the Leave "created" event.
     */
    public function created(Leave $leave): void
    {
        // Notify all admins when a new leave request is created
        $admins = User::where('role', 'admin')->get();
        
        foreach ($admins as $admin) {
            $admin->notify(new LeaveRequestNotification($leave));
        }
    }

    /**
     * Handle the Leave "updated" event.
     */
    public function updated(Leave $leave): void
    {
        // Check if statut changed to approved or rejected
        if ($leave->isDirty('statut')) {
            $newStatus = $leave->statut;
            
            if (in_array($newStatus, ['approved', 'rejected']) && $leave->user) {
                $leave->user->notify(new LeaveStatusNotification($leave, $newStatus));
            }
        }
    }
}
