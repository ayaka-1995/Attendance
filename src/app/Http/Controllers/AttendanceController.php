<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Application;
use App\Models\BreakModel;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();
        $message = null;

        $attendance = Attendance::where('user_id', $user->id)
                ->where('work_date', $today->toDateString())
                ->with('breaks')
                ->first();


        if (!$attendance){
            $status = '勤務外';
        }elseif(!$attendance->clock_out_time){
            $lastBreak = $attendance->breaks()->latest()->first();
            if($lastBreak && is_null($lastBreak->break_end_time)){
                $status = '休憩中';
            }else{
                $status = '勤務中';
            }
        }
        else{
            $status = '勤務外';
        }
        return view('attendance.index', compact('status'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $now = Carbon::now();

    if($request->has('clock_in')) {
            Attendance::create([
                'user_id' => $user->id,
                'work_date' => $now->toDateString(),
                'clock_in_time' => $now->toTimeString(),
        ]);
    }

    if($request->has('break_start')){
        $attendance = Attendance::where('user_id', $user->id)
            ->where('work_date', $now->toDateString())
            ->first();

            if($attendance){
                BreakModel::create([
                    'attendance_id' => $attendance->id,
                    'break_start_time' => $now->toTimeString(),
                ]);
            }
    }
    
    if($request->has('break_end')) {
        $attendance = Attendance::where('user_id' , $user->id)
            ->where('work_date', $now->toDateString())
            ->first();

            if($attendance){
                $lastBreak = BreakModel::where('attendance_id', $attendance->id)
                    ->whereNull('break_end_time')
                    ->latest()
                    ->first();

                    if($lastBreak){
                        $lastBreak->update([
                            'break_end_time' => $now->toTimeString(),
                        ]);
                    }
            }
    }

    if($request->has('clock_out')){
        $attendance = Attendance::where('user_id', $user->id)
            ->where('work_date', $now->toDateString())
            ->first();

            if($attendance && is_null($attendance->clock_out_time)){
                $attendance->update(['clock_out_time' => $now->toTimeString()]);
                $message = 'お疲れ様でした';
            }
    }



        return view('attendance.index');
    }

    public function list(Request $request)
    {
        $user = Auth::user();

        $month = $request->query('month', Carbon::now()->format('Y-m'));

        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth = Carbon::parse($month)->endOfMonth();

        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('work_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->orderBy('work_date','asc')
            ->with('breaks')
            ->get();

            return view('attendance.list', compact('attendances', 'month'));
    }

    public function detail($id)
    {
        $attendance = Attendance::with('user','application')->findOrFail($id);

        return view('attendance.detail', compact('attendance'));
    }

    public function applyCorrection(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $attendance->update([
            'clock_in_time' =>$request->input('clock_in_time'),
            'clock_out_time' => $request->input('clock_out_time'),
            'status' => '承認待ち',
        ]);

        $attendance->breaks()->updateOrCreate(
            [],
            [
            'break_start_time' =>$request->input('break_start'),
            'break_end_time' =>$request->input('break_end'),
        ]);

        Application::create([
            'user_id' => Auth::id(),
            'attendance_id' => $attendance->id,
            'status' => '承認待ち',
            'reason' => $request->input('reason'),
            'target_date' => $attendance->work_date,
        ]);

        return redirect()->back()->with('success','申請が送信されました(承認待ち)');
    }


}
