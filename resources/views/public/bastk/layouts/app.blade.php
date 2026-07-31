<!DOCTYPE html>
<html>

<head>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('css')

</head>

<body class="bg-slate-100">

    <div class="max-w-7xl mx-auto py-10">

        @yield('content')

    </div>

    @stack('js')

</body>

</html>
