<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="height-screen">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel Vite Vue</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="height-screen">
    <div id="app" class="height-screen"></div>
    @if (config('app.eruda'))
        <script src="//cdn.jsdelivr.net/npm/eruda"></script>
        <script>eruda.init();</script>
    @endif
</body>

</html>
