@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{asset('css/user/attendance-register.css') }}">
@endsection

@section('content')
<div class="attendance__content">
    <div class="attendance__status">
        <p class="attendance__status--item">{{ $user->attendance__status }}</p>
    </div>
    <form class="attendance_form" action="/attendance" method="post">
        @csrf
        <div class="current-date">
            <input class="current-date__item" type="text" value="{{ $formattedDate }}" readonly>
        </div>
        <div class="current-time">
            <input class="current-time__item" type="text" id="currentTime" value="{{ $formattedTime}}" readonly>
        </div>
        <div class="attendance__button">
            @if($user->attendance_status === '勤務外')
            <button class="attendance__button--submit--clock-in" type="submit" name="action" value="clock_in">出勤</button>
            @elseif($user->attendance_status === '出勤中')
            <button class="attendance__button--submit--clock-out" type="submit" name="action" value="clock_out">出勤中</button>
            @endif
        </div>
    </form>
</div>
@endsection