@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css')}}">
@endsection

@section('content')
<div class="container">
    <h2 class="heading_title">申請詳細</h2>

    <div class="detail_box">
        <div class=" detail_table">
            <table>
                <tr>
                    <th>名前</th>
                    <td>{{$application->user->name}}</td>
                </tr>
                <tr>
                    <th>日付</th>
                    <td>{{$application->target_date ? $application->target_date->format('Y年m月d日') : '未設定'}}</td>
                </tr>
                <tr>
                    <th>出勤・退勤</th>
                    <td>
                        <input type="time" value="9:00" class="time_input">〜
                        <input type="time" value="18:00" class="time_input">
                    </td>
                </tr>
                <tr>
                    <th>休憩</th>
                    <td>
                        <input type="time" value="12:00" class="time_input">〜
                        <input type="time" value="13:00" class="time_input">
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
                        <input type="text" class="remark_input" placeholder="">
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection