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
            <img class="header__logo" src="{{ asset('storage/images/logo.svg') }}" alt="ロゴ">

            {{-- 会員登録画面・ログイン画面: ロゴのみ --}}
            @if (request()->is('register') || request()->is('login'))
            {{-- その他画面 --}}
            @else
                <div class="header__menu">
                    {{-- 検索フォーム --}}
                    <form action="{{ route('search') }}" method="GET" class="header__search">
                        <input type="text" name="query" placeholder="なにをお探しですか？" class="header__search-input">
                    </form>

                    {{-- ログアウトボタン --}}
                    <form action="{{ route('logout') }}" method="POST" class="header__logout">
                        @csrf
                        <button type="submit" class="header__logout-button">ログアウト</button>
                    </form>

                    {{-- マイページボタン --}}
                    <a href="{{ route('profile.index') }}" class="header__mypage">マイページ</a>

                    {{-- 出品ボタン --}}
                    <a href="{{ route('sell') }}" class="header__sell-button">出品する</a>
                </div>
            @endif
        </div>
    </header>

    <main>
        <div class="content">
            @yield('content')
        </div>
    </main>
</body>

</html>
