<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>All Activities</title>
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="admin-body admin-detail-page">
<div class="admin-main-full activity-page">
<div class="back-btn"><button type="button" onclick="window.history.back(); return false;">Back</button></div>
<header class="management-heading">
<span>Admin</span>
<h1>Recent Activities</h1>
<p>Bookings, new listings, and new accounts.</p>
</header>
<div class="activity-list">
@forelse($activities as $activity)
@include('admin.partials.activity-row', ['activity' => $activity])
@empty
<div class="empty-state">No activities found.</div>
@endforelse
</div>
</div>
</body>
</html>
