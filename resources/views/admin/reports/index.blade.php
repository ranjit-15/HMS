@extends('admin.layout')

@section('title', 'Reports & Analytics')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-800">📊 Reports & Analytics</h1>
        <a href="{{ route('admin.analytics.index') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">
            📈 View Analytics Dashboard
        </a>
    </div>

    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        <!-- Borrowing Report -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-lg font-semibold text-slate-800 mb-4">📚 Borrowing Statistics Report</h2>
            <form method="POST" action="{{ route('admin.reports.borrowing') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Start Date</label>
                        <input type="date" name="start_date" required
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            value="{{ now()->startOfMonth()->format('Y-m-d') }}" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">End Date</label>
                        <input type="date" name="end_date" required
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            value="{{ now()->format('Y-m-d') }}" />
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" name="format" value="pdf" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        📄 Download PDF
                    </button>
                    <button type="submit" name="format" value="csv" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        📊 Download CSV
                    </button>
                </div>
            </form>
        </div>

        <!-- Hive Usage Report -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-lg font-semibold text-slate-800 mb-4">🐝 Hive Usage Report</h2>
            <form method="POST" action="{{ route('admin.reports.hive') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Start Date</label>
                        <input type="date" name="start_date" required
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            value="{{ now()->startOfMonth()->format('Y-m-d') }}" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">End Date</label>
                        <input type="date" name="end_date" required
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            value="{{ now()->format('Y-m-d') }}" />
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" name="format" value="pdf" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        📄 Download PDF
                    </button>
                    <button type="submit" name="format" value="csv" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        📊 Download CSV
                    </button>
                </div>
            </form>
        </div>

        <!-- Overdue Books Report -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-lg font-semibold text-slate-800 mb-4">⚠️ Overdue Books Report</h2>
            <p class="text-sm text-slate-500 mb-4">Generate a list of all currently overdue books with borrower details.</p>
            <div class="flex gap-2">
                <a href="{{ route('admin.reports.overdue', ['format' => 'pdf']) }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    📄 Download PDF
                </a>
                <a href="{{ route('admin.reports.overdue', ['format' => 'csv']) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    📊 Download CSV
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
