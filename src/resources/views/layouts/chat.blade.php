<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>フリマアプリ</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/chat/trading_chat.css') }}">
</head>

<body>
    <header>
        <div class="header">
        {{-- ハンバーガーボタン --}}
            <button class="hamburger" id="hamburgerBtn">
                &#9776; {{-- 三本線アイコン --}}
            </button>

        {{-- ロゴ表示 --}}
            <div class="header-left">
                <a href="{{ route('home') }}">
                    <img class="header__logo" src="{{ asset('images/app/logo.svg') }}" alt="ロゴ" />
                </a>
            </div>
        </div>
    </header>

    <div class="layout-container">
        {{-- サイドバー --}}
        <aside class="sidebar" id="sidebar">
            @yield('sidebar')
        </aside>

        {{-- メインエリア --}}
        <main>
            @yield('content')
        </main>
    </div>

    <script>
        document.getElementById('hamburgerBtn').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('open');
        });
    </script>

</body>

</html>

