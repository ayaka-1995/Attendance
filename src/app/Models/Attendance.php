<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
