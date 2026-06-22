<!DOCTYPE html>
<html>
<head>

<title>Users</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">

</head>
<body class="admin-body">

<div class="back-btn">
    <button onclick="history.back()">
        ← 
    </button>
</div>

<div class="admin-main-full">

<h1>Users Management</h1>

<div class="table-card">

<table>

<thead>

<tr>



<th>Name</th>

<th>Email</th>

<th>Status</th>

</tr>

</thead>

<tbody>

@foreach($users as $user)

<tr>



<td>{{ $user->name }}</td>

<td>{{ $user->email }}</td>

<td>

@if($user->status == 'active')

<span class="active-badge">
Active
</span>

@else

<span class="inactive-badge">
Inactive
</span>

@endif

</td>

</tr>

@endforeach

</tbody>

</table>

</div>

</div>

</body>
</html>