<?php

namespace App\Services;

use App\Models\DriverAttendence;
use App\Models\Confirmation;
use Illuminate\Support\Str;

class ConfirmationService
{
    /**
     * Approve attendance and update confirmation status.
     */
    public function approve(DriverAttendence $attendance, ?string $note = null): void
    {
        // Update the DriverAttendence record
        $attendance->forceFill([
            'is_complete' => true,
            'is_approved_in' => true,
            'is_approved_out' => true,
        ])->save();

        // Check if there is an existing confirmation to update, otherwise create one
        $confirmation = $attendance->confirmation()->latest()->first();

        if ($confirmation) {
            $confirmation->update([
                'is_confirmed' => true,
                'status' => 'approved',
                'note' => $note,
                'used_at' => now(),
            ]);
        } else {
            $attendance->confirmation()->create([
                'token' => Str::random(60),
                'is_confirmed' => true,
                'status' => 'approved',
                'note' => $note,
                'used_at' => now(),
            ]);
        }
    }

    /**
     * Reject attendance and update confirmation status with a note.
     */
    public function reject(DriverAttendence $attendance, string $note): void
    {
        // Update the DriverAttendence record
        $attendance->forceFill([
            'is_complete' => false,
            'is_approved_in' => false,
            'is_approved_out' => false,
        ])->save();

        // Check if there is an existing confirmation to update, otherwise create one
        $confirmation = $attendance->confirmation()->latest()->first();

        if ($confirmation) {
            $confirmation->update([
                'is_confirmed' => false,
                'status' => 'rejected',
                'note' => $note,
                'used_at' => now(),
            ]);
        } else {
            $attendance->confirmation()->create([
                'token' => Str::random(60),
                'is_confirmed' => false,
                'status' => 'rejected',
                'note' => $note,
                'used_at' => now(),
            ]);
        }
    }
}
