<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Login - Techspire HMS</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="min-h-screen bg-gradient-to-br from-slate-800 via-slate-900 to-slate-800 flex items-center justify-center p-4">
    <!-- Subtle background pattern -->
    <div
        class="fixed inset-0 bg-[radial-gradient(#374151_1px,transparent_1px)] [background-size:24px_24px] pointer-events-none opacity-30">
    </div>

    <div class="w-full max-w-md relative z-10">
        <div class="bg-white shadow-2xl rounded-2xl p-8 relative overflow-hidden">
            <!-- Blue gradient top bar -->
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-[#8b0000] via-[#b91c1c] to-[#8b0000]">
            </div>

            <div class="text-center mb-6">
                <div class="inline-block bg-slate-100 rounded-xl p-3 mb-4">
                    <img src="{{ asset('images/techspire-logo.png') }}" alt="Techspire College"
                        class="h-10 w-auto max-w-[180px] object-contain" loading="lazy" decoding="async" />
                </div>
                <h1 class="text-2xl font-bold text-[#8b0000]">Admin Portal</h1>
                <p class="text-sm text-slate-500 mt-1">Manage bookings, resources & settings</p>
            </div>

            @if($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('status'))
                <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5" for="email">Email Address</label>
                    <input id="email" name="email" type="email" required value="{{ old('email') }}" autocomplete="email"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-[#8b0000] focus:outline-none focus:ring-2 focus:ring-[#8b0000]/20 transition-colors"
                        placeholder="admin@techspire.edu.np" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5" for="password">Password</label>
                    <input id="password" name="password" type="password" required autocomplete="current-password"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-[#8b0000] focus:outline-none focus:ring-2 focus:ring-[#8b0000]/20 transition-colors"
                        placeholder="••••••••" />
                </div>
                <div class="flex items-center justify-between text-sm">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" value="1"
                            class="rounded border-slate-300 text-[#8b0000] focus:ring-[#8b0000]" />
                        <span class="text-slate-600">Remember me</span>
                    </label>
                </div>
                <button type="submit"
                    class="w-full rounded-lg bg-[#8b0000] px-4 py-3 text-white text-sm font-semibold hover:bg-[#6b0000] focus:outline-none focus:ring-2 focus:ring-[#8b0000] focus:ring-offset-2 transition-colors shadow-lg">
                    Sign In
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-sm text-slate-500 hover:text-[#8b0000] transition-colors">
                    ← Back to Student Login
                </a>
            </div>
        </div>

        <p class="mt-6 text-center text-xs text-slate-400">
            © {{ date('Y') }} Techspire College • Admin Portal
        </p>
    </div>

    <script>
        // Refresh CSRF token if expired
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form');
            form.addEventListener('submit', function (e) {
                // Add loading state
                const btn = form.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.innerHTML = '<span class="inline-block animate-spin mr-2">⏳</span> Signing in...';
            });
        });
    </script>
</body>

</html>