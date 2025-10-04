@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{asset ('css/application.css')}}">
@endsection

@section('content')
<div class="container">
    <div class="header">
        <h2 class="heading__title">申請一覧</h2>
</div>
    <div class="application_tabs">
        <ul class="tab_list">
            <li><a href="/">承認待ち</a></li>
            <li><a href="/">承認済み</a></li>
        </ul>
    </div>
    <div class="application_list_table">
        <table class="application_table">
            <tr class="application_list-row">
                <th class="application_status">状態</th>
                <th class="application_name">名前</th>
                <th class="target_date">対象日時</th>
                <th class="application_reason">申請理由</th>
                <th class="application_date">詳細</th>
            </tr>

            @forelse($applications as $application)
            <tr>
                <td class="application_status">
                {{$application->status === 'pending' ? '承認待ち' : '承認済み'}}
                </td>
                <td class="application_name">{{ $application->user->name }}</td>
                <td class="target_date">{{$application->target_date->format('Y-m-d H:i')}}</td>
                <td class="application_reason">{{ $application->reason}}</td>
                <td class="application_date">{{ $application->date}}</td>
            </tr>
            @empty
            <tr>
                <td class="no-application" colspan="5">申請はありません</td>
            </tr>
            @endforelse
        </table>
    </div>
</div>
@endsection