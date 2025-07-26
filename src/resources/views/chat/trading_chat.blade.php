{{-- チャット画面 --}}
@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/mypage.css') }}" />
@endsection

@section('content')

チャット画面！！！

    @foreach($order->messages as $message)

        <p>{{ $order->message}}</p>

    @endforeach

@endsection
