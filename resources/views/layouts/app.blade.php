<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Gage Tracker')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col">

{{-- Top Nav --}}
<nav class="bg-brand-800 shadow-md">
    <div class="max-w-screen-2xl mx-auto px-4 flex items-center justify-between h-14">
        <a href="{{ route('calendar.index') }}" class="text-white font-bold text-lg tracking-wide hover:text-blue-200 transition-colors">
            🔧 Gage Tracker
        </a>
        @if(session('user'))
        <div class="flex items-center gap-0.5 text-sm flex-wrap">
            <a href="{{ route('calendar.index') }}" class="text-blue-100 hover:text-white px-2 py-1 rounded transition-colors {{ request()->routeIs('calendar.*') ? 'bg-brand-600' : 'hover:bg-brand-700' }}">Calendar</a>
            <a href="{{ route('gages.index') }}" class="text-blue-100 hover:text-white px-2 py-1 rounded transition-colors {{ request()->routeIs('gages.*') ? 'bg-brand-600' : 'hover:bg-brand-700' }}">Gages</a>
            <a href="{{ route('calibrations.index') }}" class="text-blue-100 hover:text-white px-2 py-1 rounded transition-colors {{ request()->routeIs('calibrations.*') ? 'bg-brand-600' : 'hover:bg-brand-700' }}">Calibrations</a>
            <a href="{{ route('reports.index') }}" class="text-blue-100 hover:text-white px-2 py-1 rounded transition-colors {{ request()->routeIs('reports.*') ? 'bg-brand-600' : 'hover:bg-brand-700' }}">Reports</a>
            <a href="{{ route('suppliers.index') }}" class="text-blue-100 hover:text-white px-2 py-1 rounded transition-colors {{ request()->routeIs('suppliers.*') ? 'bg-brand-600' : 'hover:bg-brand-700' }}">Suppliers</a>
            <a href="{{ route('metadata.index') }}" class="text-blue-100 hover:text-white px-2 py-1 rounded transition-colors {{ request()->routeIs('metadata.*') ? 'bg-brand-600' : 'hover:bg-brand-700' }}">Metadata</a>
            <a href="{{ route('configurations.index') }}" class="text-blue-100 hover:text-white px-2 py-1 rounded transition-colors {{ request()->routeIs('configurations.*') ? 'bg-brand-600' : 'hover:bg-brand-700' }}">Config</a>
            <a href="{{ route('procedures.index') }}" class="text-blue-100 hover:text-white px-2 py-1 rounded transition-colors {{ request()->routeIs('procedures.*') ? 'bg-brand-600' : 'hover:bg-brand-700' }}">Procedures</a>
            <a href="{{ route('users.index') }}" class="text-blue-100 hover:text-white px-2 py-1 rounded transition-colors {{ request()->routeIs('users.*') ? 'bg-brand-600' : 'hover:bg-brand-700' }}">Users</a>
            <span class="text-blue-300 mx-2">|</span>
            <span class="text-blue-200 text-xs">{{ session('user.nameFirst') }}</span>
            <a href="{{ route('change-password') }}" class="text-blue-100 hover:text-white px-2 py-1 rounded text-xs hover:bg-brand-700 transition-colors">Pwd</a>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="text-blue-100 hover:text-white px-2 py-1 rounded text-xs hover:bg-brand-700 transition-colors">Logout</button>
            </form>
        </div>
        @endif
    </div>
</nav>

{{-- Flash Messages --}}
<div class="max-w-screen-2xl mx-auto w-full px-4 pt-4">
    @if(session('success'))
        <div class="flex items-center gap-2 bg-green-50 border border-green-300 text-green-800 rounded-lg px-4 py-3 text-sm mb-2">
            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-2 bg-red-50 border border-red-300 text-red-800 rounded-lg px-4 py-3 text-sm mb-2">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 border border-red-300 text-red-800 rounded-lg px-4 py-3 text-sm mb-2">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif
</div>

{{-- Main Content --}}
<main class="flex-1 max-w-screen-2xl mx-auto w-full px-4 py-4">
    @yield('content')
</main>

<footer class="text-center text-xs text-gray-400 py-3">&copy; {{ date('Y') }} Gage Tracker</footer>

</body>
</html>
