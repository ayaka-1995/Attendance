<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'user_id',
        'attendance_id',
        'status',
        'target_date',
        'reason',
        'approved_date',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

}
