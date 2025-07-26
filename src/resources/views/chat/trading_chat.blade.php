{{-- チャット画面 --}}
@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/mypage.css') }}" />
@endsection

@section('content')
    <div class="home-container">

        @foreach($messages as $message)

            <p>{{ $message->content }}</p>

        @endforeach

    </div>
@endsection
