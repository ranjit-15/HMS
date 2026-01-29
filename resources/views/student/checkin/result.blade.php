<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Check-in - Techspire HMS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 flex items-center justify-center p-6">
    <div class="w-full max-w-md rounded-lg border border-slate-200 bg-white shadow-sm p-6 text-center space-y-3">
        @if($status === 'success')
            <div class="mx-auto h-12 w-12 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-2xl">✔</div>
            <div class="text-xl font-semibold text-slate-900">Checked In</div>
            <div class="text-slate-600">{{ $message }}</div>
        @else
            <div class="mx-auto h-12 w-12 rounded-full bg-red-100 text-red-700 flex items-center justify-center text-2xl">!</div>
            <div class="text-xl font-semibold text-slate-900">Check-in Failed</div>
            <div class="text-slate-600">{{ $message }}</div>
        @endif
        <div class="text-sm text-slate-500">Table {{ $table->name }} (ID: {{ $table->id }})</div>
        <a href="{{ route('student.hive') }}" class="inline-flex justify-center rounded bg-indigo-600 px-4 py-2 text-white text-sm font-semibold hover:bg-indigo-700">Back to Hive</a>
    </div>
</body>
</html>
