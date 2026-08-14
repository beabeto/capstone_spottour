<!DOCTYPE html>
<html>
<head>

<title>Food Establishments</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">

</head>
<body>

<div class="page-container">

<div class="header-row">

<div class="back-btn">
<button onclick="history.back()">
←
</button>
</div>

<h1>

🍽️ Food Establishments

</h1>

</div>

<table>

<thead>

<tr>

<th>Image</th>
<th>Name</th>
<th>Owner</th>
<th>Location</th>
<th>Status</th>
<th>Actions</th>

</tr>

</thead>

<tbody>

@foreach($foods as $food)

<tr>

<td>

@if($food->image)

<img
src="{{ asset('uploads/foods/'.$food->image) }}"
class="table-image">

@endif

</td>

<td>{{ $food->name }}</td>

<td>{{ $food->owner->name ?? 'N/A' }}</td>

<td>{{ $food->location }}</td>

<td>{{ ucfirst($food->status) }}</td>

<td>

<a
href="/admin/foods/{{ $food->id }}"
class="view-btn">

View

</a>

<a
href="/admin/foods/approve/{{ $food->id }}"
class="approve-btn">

Approve

</a>

<a
href="/admin/foods/reject/{{ $food->id }}"
class="reject-btn">

Reject

</a>

<a
href="/admin/foods/delete/{{ $food->id }}"
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