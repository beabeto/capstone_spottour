<!DOCTYPE html>
<html>
<head>

<title>Travellers</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">

</head>
<body class="admin-body admin-detail-page">

<div class="back-btn">
    <button type="button" onclick="window.history.back(); return false;">Back</button>
</div>

<div class="admin-main-full">

<h1>Travellers</h1>

@if(session('success'))
<div class="toast-success">{{ session('success') }}</div>
@endif

<div class="table-card">

<table>

<thead>

<tr>



<th>Name</th>

<th>Email</th>

<th>Status</th>

<th>Account help</th>

</tr>

</thead>

<tbody>

@foreach($users as $user)

<tr>



<td>{{ $user->name }}</td>

<td>{{ $user->email }}</td>

<td>

@if($user->is_online)

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
<a href="/admin/accounts/{{ $user->id }}" class="view-btn">See information</a>
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
