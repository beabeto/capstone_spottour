<!DOCTYPE html>
<html>
<head>

<title>Owners</title>

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

<h1>Owners Management</h1>

<div class="table-card">

<table>

<thead>

<tr>



<th>Name</th>

<th>Email</th>

<th>Business Type</th>

<th>Status</th>

</tr>

</thead>

<tbody>

@foreach($owners as $owner)

<tr>



<td>{{ $owner->name }}</td>

<td>{{ $owner->email }}</td>

<td>{{ $owner->business_type }}</td>

<td>

@if($owner->status == 'active')

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