@extends('layouts.app')

@section('content')
<div>
    <h1>メール認証</h1>
    <p>先ほど送信したリンクをクリックして、メールアドレスを確認してください。</p>
    <p>届かない場合は、下のボタンをクリックして再送信してください。</p>

    @if (session('message'))
        <p>{{ session('message') }}</p>
    @endif

    <form method="POST" action="{{ route('verification.resend') }}">
        @csrf
        <button type="submit">確認メールを再送信する</button>
    </form>
</div>
@endsection
