<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owners</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
</head>
<body class="admin-body admin-detail-page admin-accounts-page">

<div class="back-btn">
    <button type="button" onclick="window.history.back(); return false;">Back</button>
</div>

<div class="admin-main-full">

<h1>Owners Management</h1>

@if(session('success'))
<div class="toast-success">{{ session('success') }}</div>
@endif

<div class="table-card accounts-table-card">
<table>
<thead>
<tr>
<th>Name</th>
<th>Email</th>
<th>Business Type</th>
<th>Status</th>
<th>Account help</th>
</tr>
</thead>
<tbody>
@foreach($owners as $owner)
<tr>
<td data-label="Name">{{ $owner->name }}</td>
<td data-label="Email">{{ $owner->email }}</td>
<td data-label="Business Type">{{ $owner->business_type }}</td>
<td data-label="Status">
@if($owner->is_online)
<span class="active-badge">Online</span>
@else
<span class="inactive-badge">Offline</span>
@endif
</td>
<td data-label="Account help">
<a href="/admin/accounts/{{ $owner->id }}" class="view-btn">See information</a>
</td>
</tr>
@endforeach
</tbody>
</table>
</div>

</div>

<script>setTimeout(() => window.location.reload(), 30000);</script>
@include('partials.admin-notification-float')
</body>
</html>
