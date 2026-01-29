@extends('admin.layout')

@section('title', 'Analytics')
@section('header', 'Analytics')

@section('content')
<div class="grid gap-6 md:grid-cols-2">
    <div class="rounded-lg border border-slate-200 bg-white shadow-sm p-4">
        <div class="flex items-center justify-between mb-2">
            <h2 class="text-lg font-semibold text-slate-800">Bookings per Hour</h2>
            <span class="text-xs text-slate-500">Checked-in sessions</span>
        </div>
        <canvas id="hourChart" height="200"></canvas>
    </div>
    <div class="rounded-lg border border-slate-200 bg-white shadow-sm p-4">
        <div class="flex items-center justify-between mb-2">
            <h2 class="text-lg font-semibold text-slate-800">Occupancy over the Week</h2>
            <span class="text-xs text-slate-500">Checked-in sessions</span>
        </div>
        <canvas id="dowChart" height="200"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const hourLabels = @json($hours);
    const hourData = @json($hourTotals);
    const dowLabels = @json($dowLabels);
    const dowData = @json($dowTotals);

    const colors = {
        blue: 'rgba(79, 70, 229, 0.6)',
        blueBorder: 'rgba(79, 70, 229, 1)',
        teal: 'rgba(16, 185, 129, 0.4)',
        tealBorder: 'rgba(16, 185, 129, 1)'
    };

    new Chart(document.getElementById('hourChart'), {
        type: 'bar',
        data: {
            labels: hourLabels,
            datasets: [{
                label: 'Check-ins',
                data: hourData,
                backgroundColor: colors.blue,
                borderColor: colors.blueBorder,
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true, ticks: { precision:0 } },
                x: { title: { display: true, text: 'Hour of day' } }
            }
        }
    });

    new Chart(document.getElementById('dowChart'), {
        type: 'line',
        data: {
            labels: dowLabels,
            datasets: [{
                label: 'Check-ins',
                data: dowData,
                backgroundColor: colors.teal,
                borderColor: colors.tealBorder,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true, ticks: { precision:0 } }
            }
        }
    });
</script>
@endsection
