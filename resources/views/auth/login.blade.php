<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Sign in to Techspire College - Library & Hive Management">
    <title>Welcome - Techspire College</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="min-h-screen bg-gradient-to-br from-[#8b0000] via-[#6b0000] to-[#bf4040] flex items-center justify-center p-4">
    <!-- Decorative background elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-[#f59e0b]/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-[#fbbf24]/20 rounded-full blur-3xl"></div>
    </div>

    <div class="w-full max-w-md relative z-10" role="main">
        <!-- Login Card -->
        <div class="bg-white shadow-2xl rounded-2xl p-8 relative overflow-hidden" role="region" aria-label="Login form">
            <!-- Decorative top bar - Gold accent -->
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-[#f59e0b] via-[#fbbf24] to-[#f59e0b]"
                aria-hidden="true"></div>

            <!-- Logo and Title -->
            <div class="text-center mb-8">
                <div class="inline-block bg-white rounded-xl shadow-lg p-4 mb-4 border border-slate-100">
                    <img src="{{ asset('images/techspire-logo.png') }}" alt="Techspire College"
                        class="h-12 w-auto max-w-[200px] object-contain" loading="lazy" decoding="async" />
                </div>
                <p class="text-sm text-slate-500 mt-2">Library & Hive Management System</p>
            </div>

            @if($errors->any())
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Google Sign In Button -->
            <a href="{{ route('login.google') }}"
                class="flex items-center justify-center gap-3 w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-4 text-sm font-semibold text-slate-700 shadow-sm hover:border-[#8b0000] hover:bg-[#8b0000]/5 focus:outline-none focus:ring-2 focus:ring-[#8b0000] focus:ring-offset-2 transition-all">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                        fill="#4285F4" />
                    <path
                        d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                        fill="#34A853" />
                    <path
                        d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                        fill="#FBBC05" />
                    <path
                        d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                        fill="#EA4335" />
                </svg>
                Sign in with Google
            </a>

            <p class="mt-6 text-center text-xs text-slate-500">
                Sign in using your <span class="font-medium text-[#8b0000]">@techspire.edu.np</span> account
            </p>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-200"></div>
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="px-2 bg-white text-slate-400">Features</span>
                </div>
            </div>

            <!-- Features List -->
            <div class="grid grid-cols-2 gap-3 text-center">
                <div class="p-3 rounded-lg bg-amber-50 border border-amber-100">
                    <div class="text-2xl mb-1">🐝</div>
                    <div class="text-xs font-medium text-amber-700">Hive Booking</div>
                </div>
                <div class="p-3 rounded-lg bg-blue-50 border border-blue-100">
                    <div class="text-2xl mb-1">📚</div>
                    <div class="text-xs font-medium text-blue-700">Library Access</div>
                </div>
            </div>
        </div>

        <!-- Admin link -->
        <p class="mt-6 text-center text-sm text-white/70">
            <a href="{{ route('admin.login') }}"
                class="hover:text-[#fbbf24] transition-colors inline-flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                Admin Portal
            </a>
        </p>

        <!-- Footer branding -->
        <p class="mt-8 text-center text-xs text-white/60">
            © {{ date('Y') }} Techspire College • New Baneshwor, Kathmandu
        </p>
    </div>
</body>

</html>