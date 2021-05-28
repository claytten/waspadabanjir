<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ (!empty(config('app.name')) ? config('app.name') : 'Laravel') }}</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Add to homescreen for Chrome on Android -->
    <meta name="mobile-web-app-capable" content="yes">

    <!-- Icon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/logo/apple-touch-icon.png')}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logo/favicon-32x32.png')}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/logo/favicon-16x16.png')}}">
    <link rel="manifest" href="{{ asset('images/logo/site.webmanifest')}}">
    
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('vendor/@fortawesome/fontawesome-free/css/all.min.css') }}" type="text/css">
    <!-- Styling -->
    <link type="text/css" rel="stylesheet" href="{{ asset('vendor/materialize/css/materialize.min.css')}}"  media="screen,projection"/>
    @yield('plugins_css')
    @yield('inline_css')
 </head>
 
 <body>
    @yield('content_body')
    <!-- Core -->
    <script type="text/javascript" src="{{ asset('vendor/jquery/dist/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/js-cookie/js.cookie.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/materialize/js/materialize.min.js')}}"></script>
    <!-- Optional JS -->
    @yield('plugins_js')
    @yield('inline_js')
  </body>
</html>