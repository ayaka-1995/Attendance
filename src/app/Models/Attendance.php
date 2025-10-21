<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'work_date',
        'clock_in_time',
        'clock_out_time',
        'break_start_time',
        'break_end_time',
        'comment_section',
    ];

    public function breaks()
    {
        return $this->hasMany(BreakModel::class);
    }

    public function getTotalBreakTimeAttribute()
    {
        $totalMinutes = 0;

        foreach ($this->breaks as $break) {
            if ($break->break_start_time && $break->break_end_time) {
                $start = Carbon::parse($break->break_start_time);
                $end = Carbon::parse($break->break_end_time);
                $totalMinutes += $end->diffInMinutes($start);
            }
        }

        $hours = floor($totalMinutes / 60);
        $minute = $totalMinutes % 60;

        return sprintf('%02d:%02d', $hours, $minute);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function application()
    {
        return $this->hasOne(Application::class, 'attendance_id');
    }
}
