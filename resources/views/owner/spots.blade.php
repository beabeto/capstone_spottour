<!DOCTYPE html>
<html>
<head>

<title>My Spots</title>

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

<h1>My Spots</h1>

<a
href="/owner/spots/create"
class="add-btn">

+ Add Spot

</a>

</div>

<table>

<thead>

<tr>

<th>Image</th>

<th>Name</th>

<th>Location</th>

<th>Price</th>

<th>Status</th>

<th>Action</th>

</tr>

</thead>

<tbody>

@foreach($spots as $spot)

<tr>

<td>

@if($spot->image)

<img
src="{{ asset('uploads/spots/'.$spot->image) }}"
class="table-image">

@endif

</td>

<td>
{{ $spot->name }}
</td>

<td>
{{ $spot->location }}
</td>

<td>
₱{{ $spot->price }}
</td>

<td>
{{ ucfirst($spot->status) }}
</td>

<td>

<a
href="/owner/spots/{{ $spot->id }}"
class="view-btn">

View

</a>

<a
href="/owner/spots/edit/{{ $spot->id }}"
class="edit-btn">

Edit

</a>

<a
href="/owner/spots/delete/{{ $spot->id }}"
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