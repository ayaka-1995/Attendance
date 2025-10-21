@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css')}}">
@endsection

@section('content')
<div class="container">
    <h2 class="heading_title">勤怠詳細</h2>

    <div class="detail_box">
        <div class=" detail_table">
            <table class="detail_group">
                <tr>
                    <th class="name">名前</th>
                    <td class="name">{{$attendance->user->name}}</td>
                </tr>
                <tr>
                    <th class="date">日付</th>
                    <td class="date">{{ \Carbon\Carbon::parse($attendance->work_date)->format('Y年m月d日')}}</td>
                </tr>
                <tr>
                    <th class="work_time">出勤・退勤</th>
                    <td class="work_time">
                        <input type="time" name="clock_in_time" value="{{$attendance->clock_in_time ? \Carbon\Carbon::parse($attendance->clock_in_time)->format('H:i') : ''}}" class="time_input"
                        {{ isset($attendance->application) && $attendance->application->status === '承認待ち' ? 'readonly' : '' }}>〜
                        <input type="time" name="clock_out_time" value="{{$attendance->clock_out_time ? \Carbon\Carbon::parse($attendance->clock_out_time)->format('H:i') : ''}}" class="time_input"
                        {{ isset($attendance->application) && $attendance->application->status === '承認待ち' ? 'readonly' : ''}}>
                    </td>
                </tr>
                <tr>
                    <th class="breaks">休憩</th>
                    <td class="breaks">
                    <input type="time" name="break_start" value="{{ $attendance->break_start ? \Carbon\Carbon::parse($attendance->break_start)->format('H:i') : ''}}" class="time_input"
                    {{ isset($attendance->application) && $attendance->application->status === '承認待ち' ? 'readonly' : ''}}>〜
                        <input type="time" name="break_end" value="{{ $attendance->break_end ? \Carbon\Carbon::parse($attendance->break_end)->format('H:i') : ''}}" class="time_input"
                        {{ isset($attendance->application) && $attendance->application->status === '承認待ち' ? 'readonly' : ''}}>
                    </td>
                </tr>
                <tr>
                    <th>休憩2</th>
                    <td>
                        <input type="time" class="time_input" {{ isset($attendance->application) && $attendance->application->status === '承認待ち' ? 'readonly' : ''}}>〜
                        <input type="time" class="time_input" {{ isset($attendance->application) && $attendance->application->status === '承認待ち' ? 'readonly' : ''}}>
                    </td>
                </tr>
                <tr>
                    <th class="reason">備考</th>
                    <td class="reason">
                        <input type="text" name="reason" class="remark_input" placeholder="遅延のため" 
                        {{ isset($attendance->application) && $attendance->application->status === '承認待ち' ? 'readonly' : ''}}>
                    </td>
                </tr>
            </table>

            <form class="correction_form" action="/attendance/detail/{{ $attendance->id}}" method="post">
                @csrf
                <input type="hidden" name="status" value="承認待ち">

                <div class="correction_form__button">
                    @if($attendance->application && $attendance->application->status ==='承認待ち')
                    <p class="wait_message">※承認待ちのため修正できません</p>
                    @else
                        <button class="correction_button" type="submit">修正</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection