<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Basic Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>@yield('title', 'Dashboard - SmartQueue')</title>

    <!-- SEO Meta Tags -->
    <meta name="keywords" content="Queue Management, Service Queue System, Queue Management System">
    <meta name="description" content="SmartQueue - Queue Management System">
    <meta name="author" content="SmartQueue">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- CSS Links -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">

    @stack('styles')
</head>

<body>
    @include('partials.tenant.nav')

    <main>
        @yield('content')
    </main>

    <footer class="bg-light py-4 mt-5">
        <div class="container">
            <div class="text-center text-muted small">
                <p class="mb-0">&copy; {{ date('Y') }} SmartQueue. All rights reserved.</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>

</html>

