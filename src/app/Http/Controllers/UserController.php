<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Application;
use App\Models\ApplicationBreak;
use App\Models\AttendanceRecord;
use App\Models\AttendanceBreak;
use App\Http\Requests\CorrectionRequest;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->attendance_status === '退勤済'){
            $attendance = AttendanceRecord::where('user_id', $user->id)
                    ->whereDate('date', now()->format('Y-m-d'))
                    ->first();

            if (! $attendance){
                $user->attendance_status = '勤務外';
                $user->save();
            }
        }

        $now = new \DateTime();
        $week = [
            0 => '日', 1 => '月', 2 => '火', 3 => '水',
            4 => '木', 5 => '金', 6 => '土',
        ];
        $weekday = $week[$now->format('w')];
        $formattedDate = $now->format("Y年m月d日({$weekday})");
        $formattedTime = $now->format('H:i');

        return view(
            'user/attendance-register',
            compact('formattedDate', 'formattedTime', 'user')
        );
    }

    public function attendance(Request $request)
    {
        $user = Auth::user();
        $action = $request->input('action');

        $attendance = AttendanceRecord::where('user_id', $user->id)
                ->whereDate('date', now()->toDateString())
                ->first();

        if (in_array($action, ['break_in', 'break_out', 'clock_out']) && ! $attendance){
            return redirect('/attendance')->withErrors('出勤していません。先に「出勤」をしてください。');
        }

        if ($action === 'clock_in' && $user->attendance_status ==='勤務外') {
            $attendance             = new AttendanceRecord();
            $attendance->user_id    = $user->id;
            $attendance->date       = now();
            $attendance->clock_in   = Carbon::now();
            $attendance->save();

            $user->attendance_status = '出勤中';
            $user->save();

        } elseif ($action === 'break_in' && $user->attendance_status === '出勤中') {
            $attendance->breaks()->create([
                'break_in' => Carbon::now(),
            ]);

            $user->attendance_status = '休憩中';
            $user->save();

    } elseif ($action === 'break_out'  && $user->attendance_status === '休憩中') {
        $currentBreak = $attendance
            ->breaks()
            ->whereNull('break_out')
            ->latest()
            ->first();

        if ($currentBreak){
            $currentBreak->break_out = Carbon::now();
            $currentBreak->save();
        }

        $user->attendance_status = '出勤中';
        $user->save();

    } elseif ($action === 'clock_out' && $user->attendance_status === '出勤中') {
        $attendance->clock_out = Carbon::now();

        $clockIn  = Carbon::parse($attendance->clock_in);
        $clockOut = Carbon::parse($attendance->clock_out);

        $totalBreakTime = 0;
        foreach($attendance->breaks as $b) {
            if ($b->break_in && $b->break_out) {
                $totalBreakTime +=
                    Carbon::parse($b->break_in)
                        ->diffInMinutes(Carbon::parse($b->break_out));
            }
        }

        $attendance->total_break_time = sprintf(
            '%02d:%02d',
            floor($totalBreakTime/60),
            $totalBreakTime%60
        );

        $workedMins = $clockIn->diffInMinutes($clockOut) - $totalBreakTime;
        $attendance->total_time = sprintf(
            '%02d:%02d',
            floor($workedMins/60),
            $workedMins%60
        );

        $attendance->save();

        $user->attendance_status = '退勤済';
        $user->save();
    }

    return redirect('/attendance');
    }

    public function list(Request $request)
    {
        $user = Auth::user();
        $date = Carbon::parse($request->query('date', now()));

        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        $attendanceRecords = AttendanceRecord::where('user_id', $user->id)
            ->whereBetween('date',[$startOfMonth, $endOfMonth])
            ->get();


        $formatted = $attendanceRecords->map(function ($rec){
            $weekdays = ['日', '月', '火', '水', '木', '金', '土'];
            $d = Carbon::parse($rec->date);
            return [
                'id'               => $rec->id,
                'date'             => $d->format('m/d') . "({$weekdays[$d->dayOfWeek]})",
                'clock_in'         => $rec->clock_in ? Carbon::parse($rec->clock_in)->format('H:i') : null,
                'clock_out'        => $rec->clock_out ? Carbon::parse($rec->clock_out)->format('H:i') :null,
                'total_time'       => $rec->total_time,
                'total_break_time' => $rec->total_beak_time,
            ];
        });

        return view('user/user-attendance-list', [
            'formattedAttendanceRecords' => $formatted,
            'date'         => $date,
            'nextMonth' => $date->copy()->addMonth()->format('Y-m'),
            'previousMonth' => $date->copy()->subMonth()->format('Y-m'),
        ]);
    }
}