@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css')}}">
@endsection

@section('content')
<div class="container">
    <h2 class="heading_title">勤怠詳細</h2>

    <div class="detail_box">
        <div class=" detail_table">
            <table>
                <tr>
                    <th>名前</th>
                    <td>{{$attendance->user->name}}</td>
                </tr>
                <tr>
                    <th>日付</th>
                    <td>{{$attendance->date ? $attendance->date->format('Y年m月d日') : ''}}</td>
                </tr>
                <tr>
                    <th>出勤・退勤</th>
                    <td>
                        <input type="time" name="start_time" value="{{$attendance->start_time ? \Carbon\Carbon::parse($attendance->start_time)->format('H:i') : ''}}" class="time_input">〜
                        <input type="time" name="end_time" value="{{$attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : ''}}" class="time_input">
                    </td>
                </tr>
                <tr>
                    <th>休憩</th>
                    <td>
                    <input type="time" name="break_start" value="{{ $attendance->break_start ? \Carbon\Carbon::parse($attendance->break_start)->format('H:i') : ''}}" class="time_input">〜
                        <input type="time" name="break_end" value="{{ $attendance->break_end ? \Carbon\Carbon::parse($attendance->break_end)->format('H:i') : ''}}" class="time_input">
                    </td>
                </tr>
                <tr>
                    <th>休憩2</th>
                    <td>
                        <input type="time" class="time_input">〜
                        <input type="time" class="time_input">
                    </td>
                </tr>
                <tr>
                    <th>備考</th>
                    <td>
                        <input type="text" name="reason" class="remark_input" placeholder="遅延のため">
                    </td>
                </tr>
            </table>
            <form class="correction_form" action="/attendance/detail/{{ $attendance->id}}" method="post">
                @csrf
                <input type="hidden" name="status" value="承認待ち">
                <div class="correction_form__button">
                    <button class="correction_button" type="submit">修正</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection