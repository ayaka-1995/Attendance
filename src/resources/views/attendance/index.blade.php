@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="attendance-form__content">
    <div class="attendance-form__heading">
        <h2>勤務外</h2>
    </div>

    <div class="attendance__date">
        {{ now()->format('Y年n月j日(D)')}}
    </div>

    <div class="attendance__time">
        {{ now()->format('H:i')}}
    </div>

    <div class="attendance__button">
        <form action="/attendance" method="POST">
            @csrf
            <button type="submit" class="attendance__button-submit">出勤</button>
        </form>
    </div>
</div>
@endsection
