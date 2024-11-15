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
            <img class="header__logo" src="{{ asset('storage/images/logo.svg') }}" alt="ロゴ">
        </div>
    </header>

    <main>
        <div class="content">
            @yield('content')
        </div>
    </main>
</body>

</html>
