@extends('adminlte::page')

@section('title', 'Customer Dashboard')

@section('content_header')
    <h1>Dashboard for {{ $customer->name }}</h1>
@endsection

@section('content')
<div class="row">
    <!-- Monthly Order Summary -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">Monthly Order Summary</div>
            <div class="card-body">
                <ul>
                    @foreach ($monthlyOrderSummary as $summary)
                        <li>{{ $summary->month }}: {{ $summary->total_orders }} orders</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Monthly Square Meter Summary -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success text-white">Monthly Square Meter Summary</div>
            <div class="card-body">
                <ul>
                    @foreach ($monthlySquareMeterSummary as $summary)
                        <li>{{ $summary->month }}: {{ number_format($summary->total_squaremeter, 2) }} m²</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Monthly Orders by Product -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-warning text-white">Monthly Orders by Product</div>
            <div class="card-body">
                <ul>
                    @foreach ($monthlyOrdersByProduct as $summary)
                        <li>{{ $summary->month }} - {{ $summary->product_name }}: {{ $summary->total_orders }} orders</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Monthly Square Meter Summary per Product -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-danger text-white">Monthly Square Meter Summary per Product</div>
            <div class="card-body">
                <ul>
                    @foreach ($monthlySquareMeterByProduct as $summary)
                        <li>{{ $summary->month }} - {{ $summary->product_name }}: {{ number_format($summary->total_squaremeter, 2) }} m²</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <!-- Yearly Order Summary -->
    <div class="col-md-3">
        <div class="card">
            <div class="card-header bg-primary text-white">Yearly Order Summary</div>
            <div class="card-body">
                <canvas id="yearlyOrderSummaryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Yearly Square Meter Summary -->
    <div class="col-md-3">
        <div class="card">
            <div class="card-header bg-success text-white">Yearly Square Meter Summary</div>
            <div class="card-body">
                <canvas id="yearlySquareMeterSummaryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Yearly Orders by Product -->
    <div class="col-md-3">
        <div class="card">
            <div class="card-header bg-warning text-white">Yearly Orders by Product</div>
            <div class="card-body">
                <canvas id="yearlyOrdersByProductChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Yearly Square Meter Summary per Product -->
    <div class="col-md-3">
        <div class="card">
            <div class="card-header bg-danger text-white">Yearly Square Meter Summary per Product</div>
            <div class="card-body">
                <canvas id="yearlySquareMeterByProductChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Yearly Order Summary Chart
        const yearlyOrderSummaryCtx = document.getElementById('yearlyOrderSummaryChart').getContext('2d');
        new Chart(yearlyOrderSummaryCtx, {
            type: 'pie',
            data: {
                labels: @json($yearlyOrderSummary->pluck('year')),
                datasets: [{
                    data: @json($yearlyOrderSummary->pluck('total_orders')),
                    backgroundColor: ['#007bff', '#6c757d', '#28a745', '#dc3545', '#ffc107']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                },
            },
        });

        // Yearly Square Meter Summary Chart
        const yearlySquareMeterSummaryCtx = document.getElementById('yearlySquareMeterSummaryChart').getContext('2d');
        new Chart(yearlySquareMeterSummaryCtx, {
            type: 'pie',
            data: {
                labels: @json($yearlySquareMeterSummary->pluck('year')),
                datasets: [{
                    data: @json($yearlySquareMeterSummary->pluck('total_squaremeter')),
                    backgroundColor: ['#007bff', '#6c757d', '#28a745', '#dc3545', '#ffc107']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                },
            },
        });

        // Yearly Orders by Product Chart
        const yearlyOrdersByProductCtx = document.getElementById('yearlyOrdersByProductChart').getContext('2d');
        new Chart(yearlyOrdersByProductCtx, {
            type: 'pie',
            data: {
                labels: @json($yearlyOrdersByProduct->pluck('product_name')),
                datasets: [{
                    data: @json($yearlyOrdersByProduct->pluck('total_orders')),
                    backgroundColor: ['#007bff', '#6c757d', '#28a745', '#dc3545', '#ffc107']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                },
            },
        });

        // Yearly Square Meter Summary per Product Chart
        const yearlySquareMeterByProductCtx = document.getElementById('yearlySquareMeterByProductChart').getContext('2d');
        new Chart(yearlySquareMeterByProductCtx, {
            type: 'pie',
            data: {
                labels: @json($yearlySquareMeterByProduct->pluck('product_name')),
                datasets: [{
                    data: @json($yearlySquareMeterByProduct->pluck('total_squaremeter')),
                    backgroundColor: ['#007bff', '#6c757d', '#28a745', '#dc3545', '#ffc107']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                },
            },
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection