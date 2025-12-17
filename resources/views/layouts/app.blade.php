<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Basic Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>@yield('title', 'SmartQueue - Queue Management System')</title>

    <!-- SEO Meta Tags -->
    <meta name="keywords" content="Queue Management, Service Queue System, Queue Management System">
    <meta name="description"
        content="SmartQueue - Modern queue management system. Streamline service flow and improve efficiency.">
    <meta name="author" content="SmartQueue">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- CSS Links -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">

    @stack('styles')
    <style>
        .navbar-brand span {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .nav-link:hover {
            color: #0d6efd !important;
        }

        footer p {
            font-size: 0.9rem;
        }
    </style>

</head>

<body>
    <header class="header_section">
        @include('partials.header')
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="footer_section">
        @include('partials.footer')
    </footer>

    @stack('scripts')
</body>

</html>