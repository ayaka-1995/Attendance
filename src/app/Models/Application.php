<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model//修正の申請
{
    protected $fillable = [
        'user_id',
        'attendance_record_id',//どの勤怠に対する申請か
        'approval_status',//承認待ち、承認済み、却下
        'application_date',
        'new_date',
        'new_clock_in',
        'new_clock_out',
        'comment'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);//誰の申請か
    }

    public function attendanceRecord()//どの勤怠を修正したいのか
    {
        return $this->belongsTo(AttendanceRecord::class);
    }

    public function proposalBreaks()//修正後の休憩案が複数ある場合
    {
        return $this->hasMany(ApplicationBreak::class);
    }

}
