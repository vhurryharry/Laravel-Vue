<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title')</title>

  <link href="{{ asset('css/app.css') }}" rel="stylesheet" />

</head>

@if(str_replace('_', '-', app()->getLocale()) === 'ar' )

<body dir="rtl">
  @else

  <body>
    @endif
    <div id="app" class="font-sans antialiased w-full h-full leading-tight">
      @include('layouts.header')

      <div>@yield('content')</div>
    </div>

    <script src="{{ mix('js/app.js') }}"></script>

    <script type="text/javascript">
      (function() {
        var s = document.createElement("script");
        s.type = "text/javascript";
        s.async = true;
        s.src = '//api.usersnap.com/load/5aead503-a35f-42af-b363-d06e43b1eb4c.js';
        var x = document.getElementsByTagName('script')[0];
        x.parentNode.insertBefore(s, x);
      })();
    </script>
  </body>

</html>