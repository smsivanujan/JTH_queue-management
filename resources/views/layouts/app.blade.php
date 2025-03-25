<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Basic Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Teaching Hospital Jaffna')</title>

    <!-- SEO Meta Tags -->
    <meta name="keywords" content="Health Information Unit, Teaching Hospital Jaffna">
    <meta name="description"
        content="Health Information Unit (HIU) at Teaching Hospital Jaffna ensures reliable IT infrastructure for efficient healthcare delivery.">
    <meta name="author" content="Health Information Unit, Teaching Hospital Jaffna">

    <!-- CSS Links -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">

    @stack('styles')
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
