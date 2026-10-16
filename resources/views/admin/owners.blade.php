<!DOCTYPE html>
<html>
<head>

<title>Owners</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">

</head>
<body class="admin-body admin-detail-page">

<div class="back-btn">
    <button type="button" onclick="window.history.back(); return false;">Back</button>
</div>

<div class="admin-main-full">

<h1>Owners Management</h1>

@if(session('success'))
<div class="toast-success">{{ session('success') }}</div>
@endif

<div class="table-card">

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



<td>{{ $owner->name }}</td>

<td>{{ $owner->email }}</td>

<td>{{ $owner->business_type }}</td>

<td>

@if($owner->is_online)

<span class="active-badge">
Online
</span>

@else

<span class="inactive-badge">
Offline
</span>

@endif

</td>

<td>
<a href="/admin/accounts/{{ $owner->id }}" class="view-btn">See information</a>
</td>

</tr>

@endforeach

</tbody>

</table>

</div>

</div>

<script>setTimeout(() => window.location.reload(), 30000);</script>
</body>
</html>
