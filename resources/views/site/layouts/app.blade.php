<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <link rel="icon" href="{{ asset('storage/'.$settings->fav_icon)}}" type="image/webp"/>
    <title>@yield(section: 'title')</title>
    @include('site.includes.styles')
</head>
<body>
    <!-- Header Section -->
    @include('site.includes.header')
    <!-- Banner/Swiper Section -->
    @include('site.includes.swiper')

    @yield('content')

    <!-- Footer Section -->
    @include('site.includes.footer')

    <!-- Bootstrap JS Bundle -->
    @include('site.includes.scripts')
</body>
</html>
