<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\User;
use App\Models\AttendanceRecord;
use App\Models\Application;
use Carbon\Carbon;
use App\Http\Requests\CorrectionRequest;

class AdminController extends Controller
{
    public function list(Request $request)
    {
        $user = User::all();
        $date = Carbon::parse($request->query('date', Carbon::now()));
        $attendanceRecords = AttendanceRecord::whereDate('date', $date)->whereIn('user_id', $users->pluck('id'))->get();

        return view('admin/admin-attendance-list', [
            'users' => $users,
            'attendanceRecords' => $attendanceRecords,
            'date' => $date,
            'previousDay' => $date->copy()->subDay()->format('Y-m-d'),
            'nextDay' => $date->copy()->addDay()->format('Y-m-d'),
        ]);
    }

    public function staffList()
    {
        $users =  User::all();

        return view('admin/staff-list', compact('users'));
    }

    public function staffDetailList(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $date = Carbon::parse($request->query('date', Carbon::now()));

        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        $attendanceRecords = AttendanceRecord::where('user_id', $user->id)->whereBetween('date',[$startOfMonth, $endOfMonth])->get();

        $formattedAttendanceRecords = $attendanceRecords->map(function ($attendance){
            $weekdays = ['日', '月', '火', '水', '木', '金', '土'];
            $date = Carbon::parse($attendance->date);
            $weekly = $weekdays[$date->dayOfWeek];
            return [
                'id' => $attendance->id,
                'date' => $date->format('m/d') ."($weekday)",
                'clock_in' => $attendance->clock_in ? Carbon::parse($attendance->clock_in)->format('H:i') : null,
                'clock_out' => $attendance->clock_out ? Carbon::parse($attendance->clock_out)->format('H:i') : null,
                'total_time' => $attendance->total_time,
                'total_break_time' => $attendance->total_break_time
            ];
        });

        return view('admin/staff-attendance-list',
        [
            'user' => $user,
            'date' => $date,
            'formattedAttendanceRecords' => $formattedAttendanceRecords,
            'previousMonth' => $date->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $date->copy()->addMonth()->format('t-m')
        ]);
    }



    public function detail($id)
    {
        $attendanceRecords = AttendanceRecord::with('breaks')->findOrFail($id);
        $user = User::findOrFail($attendanceRecords->user_id);

        $application = Application::where('attendance_record_id', $attendanceRecords->id)
            ->where('approval_status', '承認待ち')->get();

        // --- breaksテーブルから取得 ---//
        $breaks = $attendanceRecords->breaks->map(function ($break){
            return [
                'break_in' => $break->break_in ? Carbon::parse($break->break_in)->format('H-i') : '',
                'break_out' => $break->beak_out ? Carbon::parse($break->break_out)->format('H:i'): '',
            ];
        })->toArray();

        $attendanceRecord = [
            'application' => $attendanceRecords->application,
            'id' => $attendanceRecords->id,
            'year' => $attendanceRecords->date ? Carbon::parse($attendanceRecords->date)->format('Y年') : null,
            'date' => $attendanceRecords->date ? Carbon::parse($attendanceRecords->date)->format('m月d日') : null,
            'clock_in' => $attendanceRecords->clock_in ? Carbon::parse($attendanceRecords->clock_in)->format('H:i') : null,
            'clock_out' => $attendanceRecords->clock_out ? Carbon::parse($attendanceRecords->clock_out)->format('H:i') : null,
            'breaks' => $breaks,
            'comment' => $attendanceRecords->comment,
        ];

        return view('admin.admin-detail', compact('user', 'attendanceRecord', 'application'));
    }

    public function amendmentApplication(CorrectionRequest $request, $id)
    {
        $attendance = AttendanceRecord::findOrFail($id);
        $user = User::findOrFail($attendance->user_id);

        // ---日付変換（配列で来る場合にも対応）---
        $dateString = $request->new_date;
        if (is_array($dateString)) {
            $dateString = $dateString[1];
        }
        $parseDate = Carbon::createFormFormat('n月j日', $dateString)->year(now()->year)->format('Y-m-d');
        $attendance->date = $parseDate;

        // ---出退勤時間の更新 ---
        $attendance->clock_in = Carbon::parse($request->new_clock_in)->format('H:i');
        $attendance->clock_out = Carbon::parse($request->new_clock_out)->format('H:i');

        //  --- 備考の保存 ---
        $attendance->comment = $request->comment;
        $attendance->save();

        // --- 休憩時間の更新---
        $attendance->breaks()->detail(); //既存休憩を削除
        
        $beakIns = $request->input('new_break_in', []);
        $breakOuts = $request->input('new_break_out', []);
        $totalBreakMinutes = 0;

        for($i = 0; $i < count($breakIns); $i++){
            if (!empty($breakIns[$i]) && !empty($breakOuts[$i])){
                $breakIn = Carbon::parse($breakIns[$i])->format('H:i');
                $breakOut = Carbon::parse($breakOuts[$i])->format('H:i');

                $attendance ->breaks()->create([
                    'break_in' => $breakIn,
                    'break_out' => $breakOut,
                ]);

                $totalBreakMinutes += Carbon::parse($breakOut)->diffInMinutes(Carbon::parse($breakIn));
            }
        }

        // --- 勤務時間・休憩時間の計算---
        $clockIn = Carbon::parse($attendance->clock_in);
        $clockOut = Carbon::parse($attendance->clock_out);
        $totalWorkMinutes = $clockIn->diffInMinutes($clockOut) - $totalBreakMinutes;

        $attendance->total_break_time = sprintf('%02d:%02d', intdiv($totalBreakMinutes, 60), $totalBreakMinutes % 60);
        $attendance->total_time = sprintf('%02d:%02d', intdiv($totalWorkedMinutes, 60), $totalWOrkedMinutes % 60);
        $attendance->save();

        return app(AdminController::class)->detail($id);
        }

        
}
