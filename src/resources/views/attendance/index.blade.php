@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="attendance-form__content">
    <div class="attendance-form__heading">
        <h2>{{ $status }}</h2>
    </div>

    <div class="attendance__date">
        {{ now()->format('Y年n月j日(D)')}}
    </div>

    <div class="attendance__time">
        {{ now()->format('H:i')}}
    </div>

    <div class="attendance__button">
        @if($status === '勤務外')
        <form action="/attendance" method="POST">
            @csrf
            <button type="submit" name="clock_in" class="attendance__button-submit">出勤</button>
        </form>
        @elseif($status === '勤務中')
        <form action="/attendance" method="POST">
            @csrf
            <button type="submit" name="break_start" class="attendance__button-button">休憩入</button>
        </form>

        <form action="/attendance" method="POST">
            @csrf
            <button type="submit" name="clock_out" class="attendance__button-submit">退勤</button>
        </form>

        @elseif($status === '休憩中')
        <form action="/attendance" method="POST">
            @csrf
            <button type="submit" name="break_end" class="attendance__button-button">休憩戻</button>
        </form>
        @endif
    </div>
</div>
@endsection
