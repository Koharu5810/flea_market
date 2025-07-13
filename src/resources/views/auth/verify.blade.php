@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/verify.css') }}" />
@endsection

@section('content')
<div class="verify-container">
    <p class="verify-message">
        登録していただいたメールアドレスに認証メールを送付しました。<br>
        メール認証を完了してください。
    </p>

    <button class="verify-button">認証はこちらから</button>

    @if (session('message'))
        <p>{{ session('message') }}</p>
    @endif

    <form method="POST" action="{{ route('verification.resend') }}">
        @csrf
        <button class="blue-button resend-button" type="submit">認証メールを再送する</button>
    </form>
</div>
@endsection
