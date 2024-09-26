<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log; // Add this to log success message

class TimeChangeLog extends Model
{
    use HasFactory;

    protected $fillable = ['attendances_id', 'time_sheet_id', 'changed_by', 'reason','type'];

    public function logaudit($attendance_id, $timesheet_id, $changed_by, $reason, $type)
    {
        try {
            // Save the deletion reason and user name to the database
            TimeChangeLog::create([
                'attendances_id' => $attendance_id,
                'time_sheet_id' => $timesheet_id,
                'changed_by' => $changed_by,
                'reason' => $reason,
                'type' => $type,
            ]);

            // Return success message
            return 'TimeChangeLog created successfully.';
        } catch (\Exception $e) {
            // Return failure message
            return 'Failed to create TimeChangeLog: ' . $e->getMessage();
        }
    }

    public function timeSheet()
    {
        return $this->belongsTo(TimeSheet::class, 'time_sheet_id', 'id');
    }
    
}

