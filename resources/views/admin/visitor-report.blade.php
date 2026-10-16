<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor report | SpotTour</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="admin-body admin-detail-page report-page">
<div class="back-btn"><button type="button" onclick="window.history.back(); return false;">Back</button></div>
<main class="admin-main-full">
    <header class="management-heading">
        <span>Tourism office</span>
        <h1>Visitor report</h1>
        <p>Bookings and actual arrivals for each establishment in {{ $month->format('F Y') }}.</p>
    </header>

    <form method="GET" action="/admin/reports" class="report-toolbar">
        <label>Month
            <input type="month" name="month" value="{{ $month->format('Y-m') }}">
        </label>
        <button type="submit">View report</button>
        <a class="view-btn" href="/admin/reports/pdf?month={{ $month->format('Y-m') }}">Download PDF</a>
    </form>

    <section class="report-summary" aria-label="Month totals">
        <div>
            <span>Establishments</span>
            <strong>{{ $rows->count() }}</strong>
        </div>
        <div>
            <span>Booked</span>
            <strong>{{ $rows->sum('bookings') }}</strong>
        </div>
        <div>
            <span>Arrived</span>
            <strong>{{ $rows->sum('arrived') }}</strong>
        </div>
        <div>
            <span>Approved revenue</span>
            <strong>PHP {{ number_format($rows->sum('revenue'), 2) }}</strong>
        </div>
    </section>

    <section class="table-card booking-table-card">
        <h2>{{ $month->format('F Y') }} by establishment</h2>
        <div class="table-scroll"><table>
            <thead><tr><th>Establishment</th><th>Type</th><th>Booked</th><th>Arrived</th><th>Approved revenue</th></tr></thead>
            <tbody>
            @forelse($rows as $row)
                <tr>
                    <td><strong>{{ $row['name'] }}</strong></td>
                    <td><span class="report-kind">{{ $row['kind'] }}</span></td>
                    <td>{{ $row['bookings'] }}</td>
                    <td>{{ $row['arrived'] }}</td>
                    <td>PHP {{ number_format($row['revenue'], 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty-table">No bookings in {{ $month->format('F Y') }}.</td></tr>
            @endforelse
            </tbody>
        </table></div>
    </section>
</main>
</body>
</html>
