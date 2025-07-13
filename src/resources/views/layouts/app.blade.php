<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>フリマアプリ</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    <header>
        <div class="header">
        {{-- ロゴ表示 --}}
            <div class="header-left">
                <a href="{{ route('home') }}">
                    <img class="header__logo" src="{{ asset('storage/app/logo.svg') }}" alt="ロゴ" />
                </a>
            </div>

            @php
                $hideOnPages = request()->is('register') ||
                            request()->is('login') ||
                            request()->is('verify*') ||
                            request()->is('email/verify*');
            @endphp

            {{-- ログイン・会員登録画面では非表示 --}}
            @if (!$hideOnPages)
                <div class="header-center">
                    {{-- 検索フォーム --}}
                    <form action="{{ route('search') }}" method="GET" class="header__search">
                        <input
                            type="text"
                            name="query"
                            value="{{ request('query') }}"
                            placeholder="なにをお探しですか？"
                            class="header__search-input"
                        />
                    </form>
                </div>
            @endif

    {{-- ボタンコンテナ --}}
            @if (!$hideOnPages)
                <div class="header-right">
            {{-- ログイン・ログアウトボタン --}}
                    @if (Auth::check())
                        <form action="{{ route('logout') }}" method="POST" class="header__logout">
                            @csrf
                            <button type="submit" class="header__logout-button">ログアウト</button>
                        </form>
                    @else
                        <form action="{{ route('show.login') }}" method="GET" class="header__login">
                            @csrf
                            <button type="submit" class="header__login-button">ログイン</button>
                        </form>
                    @endif
            {{-- マイページボタン --}}
                    <button class="header__mypage-button"><a href="{{ route('profile.mypage') }}">マイページ</a></button>
            {{-- 出品ボタン --}}
                    <button class="header__sell-button"><a href="{{ route('sell') }}">出品</a></button>
                </div>
            @endif
        </div>
    </header>

    <main>
        <div class="content">
            <h1 class="content__title">@yield('title')</h1>
            @yield('content')
        </div>
    </main>
</body>

</html>
