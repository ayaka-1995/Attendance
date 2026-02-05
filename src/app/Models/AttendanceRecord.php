<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AttendanceBreak;

class AttendanceRecord extends Model//１日分の勤怠（勤怠の本体）
{
    //use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_out',
        'total_time',//総労働時間
        'total_break_time',
        'comment'//管理者コメント等
    ];

    protected $casts = [//DB、PHP の型変換のルール  「このカラムは、日付？時刻？文字列？」をLaravelに教える辞書
        'date'             =>'datetime',
        'clock_in'         =>'datetime:H:i',
        'clock_out'        =>'datetime:H:i',
        'total_time'       =>'string',
        'total_break_time' =>'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);//この勤怠に対して、複数回申請があり得る
    }

    public function breaks()
    {
        return $this->hasMany(AttendanceBreak::class);//実際にとった休憩
    }
}
