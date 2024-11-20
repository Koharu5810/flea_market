{{-- 商品一覧画面 --}}
@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/index.css') }}" />
@endsection

@section('content')
    <div class="myPage-container">
        <div class="myPage__header">
            おすすめ
            マイリスト
        </div>
    {{-- プロフィールヘッダー --}}
        <div class="myPage__product">

        </div>
    </div>
@endsection
