<!DOCTYPE html>
<html>
<head>

<title>My Food Establishments</title>

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

    My Food Establishments

</h1>

<a
href="/owner/foods/create"
class="add-btn">

    + Add Food Establishment

</a>

</div>

<table>

<thead>

<tr>

<th>Image</th>

<th>Name</th>

<th>Location</th>

<th>Tables</th>

<th>Venue Capacity</th>

<th>Status</th>

<th>Action</th>

</tr>

</thead>

<tbody>

@forelse($foods as $food)

<tr>

<td>

@if($food->image)

<img
src="{{ asset('uploads/foods/'.$food->image) }}"
class="table-image">

@else

No Image

@endif

</td>

<td>

    {{ $food->name }}

</td>

<td>

    {{ $food->location }}

</td>

<td>

    {{ $food->available_tables }}

</td>

<td>

    {{ $food->venue_capacity }}

</td>

<td>

<span class="
@if($food->status == 'approved')
status-approved
@elseif($food->status == 'rejected')
status-rejected
@else
status-pending
@endif
">

{{ ucfirst($food->status) }}

</span>

</td>

<td>

<a
href="/owner/foods/{{ $food->id }}"
class="view-btn">

    View

</a>

<a
href="/owner/foods/{{ $food->id }}/menus"
class="menu-btn">

    🍽 Menu

</a>

<a
href="/owner/foods/edit/{{ $food->id }}"
class="edit-btn">

    Edit

</a>

<a
href="/owner/foods/delete/{{ $food->id }}"
class="deletes-btn"
onclick="return confirm('Delete this food establishment?')">

    Delete

</a>

</td>

</tr>

@empty

<tr>

<td colspan="7">

    No Food Establishments Found

</td>

</tr>

@endforelse

</tbody>

</table>

</div>

</body>
</html>