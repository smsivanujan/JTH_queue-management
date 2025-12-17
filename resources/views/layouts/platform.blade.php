<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Basic Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>@yield('title', 'Platform Dashboard - SmartQueue')</title>

    <!-- SEO Meta Tags -->
    <meta name="keywords" content="Queue Management, Platform Admin, Super Admin">
    <meta name="description" content="SmartQueue - Platform Administration Panel">
    <meta name="author" content="SmartQueue">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- CSS Links -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">

    @stack('styles')
</head>

<body>
    @include('partials.platform.nav')

    <main>
        @yield('content')
    </main>

    @stack('scripts')
</body>

</html>

