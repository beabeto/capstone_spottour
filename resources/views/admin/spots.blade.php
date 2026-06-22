<!DOCTYPE html>

<html>
<head>

<title>Manage Spots</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">

</head>
<body>

<div class="page-container">

<div class="back-btn">
    <button onclick="history.back()">
        ← 
    </button>
</div>

<br></br>


<h1>Manage Spots</h1>

<table>

<thead>

<tr>

<th>Owner</th>

<th>Spot Name</th>

<th>Location</th>

<th>Status</th>

<th>Action</th>

</tr>

</thead>

<tbody>

@foreach($spots as $spot)

<tr>

<td>

{{ $spot->owner->name ?? 'Unknown' }}

</td>

<td>

{{ $spot->name }}

</td>

<td>

{{ $spot->location }}

</td>

<td>

{{ ucfirst($spot->status) }}

</td>

<td>

<a
href="/admin/spots/{{ $spot->id }}"
class="view-btn">

View

</a>

<a
href="/admin/spots/approve/{{ $spot->id }}"
class="approve-btn">

Approve

</a>

<a
href="/admin/spots/reject/{{ $spot->id }}"
class="reject-btn">

Reject

</a>

<a
href="/admin/spots/delete/{{ $spot->id }}"
class="deletes-btn">

Delete

</a>

</td>

</tr>

@endforeach

</tbody>

</table>

</div>

</body>
</html>