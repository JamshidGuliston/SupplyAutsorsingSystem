<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <title>Addelkadir — @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @stack('head')
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <span class="navbar-brand">Addelkadir paneli</span>
        <div>
            <a class="text-white me-3" href="{{ route('addelkadir.home') }}">Bosh</a>
            <a class="text-white me-3" href="{{ url('addelkadir/attendance') }}">Davomat</a>
            <a class="text-white me-3" href="{{ url('addelkadir/kindgardens') }}">Bog'chalar</a>
            <a class="text-white me-3" href="{{ url('addelkadir/chefs') }}">Oshpazlar</a>
            <a class="text-white" href="{{ route('logout') }}"
               onclick="event.preventDefault();document.getElementById('logout-form').submit();">Chiqish</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </div>
    </div>
</nav>
<main class="container py-4">
    @yield('content')
</main>
</body>
</html>
