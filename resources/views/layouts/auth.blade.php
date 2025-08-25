<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'MediGest')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-gradient-to-br from-amber-50 via-white to-emerald-50 text-gray-900">
  <main class="flex-1">
    @yield('content')
  </main>
</body>
</html>
