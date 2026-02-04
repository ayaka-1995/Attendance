<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model//修正の申請
{
    protected $fillable = [
        'user_id',
        'attendance_id',//どの勤怠に対する申請か
        'status',//承認待ち、承認済み、却下
        'target_date',
        'reason',
        'approved_date',//承認日
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
