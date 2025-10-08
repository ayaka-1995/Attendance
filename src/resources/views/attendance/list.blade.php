@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{asset ('css/list.css')}}">
@endsection

@section('content')
<div class="container">
    <div class="header">
        <h2 class="heading__title">勤怠一覧</h2>
    </div>
    
    @php
    $current = \Carbon\Carbon::parse($month);
    $prevMonth = $current->copy()->subMonth()->format('Y-m');
    $nextMonth = $current->copy()->addMonth()->format('Y-m');
    @endphp

    <div class="month-navigation">
        <a href="{{ route('attendance.list',['month' => $prevMonth])}}" class="nav-link">
            &larr; 前月
        </a>

        <div class="current-month">
            🗓️{{ $current->format ('Y/m') }}
        </div>

        <a href="{{ route('attendance.list',['month' => $nextMonth]) }}" class="nav-link">
            翌月 &rarr;
        </a>
    </div>

    <div class="attendance__list-table">
        <table class="attendance-table">
            <tr class="list__table-header_row">
            <th class="attendance__table-header__date">日付</th>
            <th class="attendance__table-header__work-in-time">出勤</th>
            <th class="attendance__table-header__work-out-time">退勤</th>
            <th class="attendance__table-header__bleak-time">休憩時間</th>
            <th class="attendance__table-header__total">合計</th>
            <th class="attendance__table-header__comment-section">詳細</th>
        </tr>
            @foreach($attendances as $attendance)
                <tr class="attendance-row">
                    <td class="attendance_work_date-content">{{$attendance->work_date}}</td>
                    <td class="attendance_clock_in_time">{{ $attendance->clock_in_time}}</td>
                    <td class="attendance_clock_out_time">{{ $attendance->clock_out_time}}</td>
                    <td class="attendance_break_time">{{ $attendance->total_break_time}}</td>
                    <td class="attendance_work_total"></td>
                    <td class="attendance_detail"><a href="{{ route('attendance.detail',['id' => $attendance->id]) }}" class="detail_link">詳細</a></td>
                </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection