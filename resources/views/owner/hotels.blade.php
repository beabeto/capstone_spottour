<!DOCTYPE html>

<html>
<head>

<title>My Hotels</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">

</head>

<body>

<div class="page-container">

<div class="header-row">

<div class="back-btn">
    <button onclick="history.back()">
        ← Back
    </button>
</div>

<h1>

    My Hotels

</h1>

<a
href="/owner/hotels/create"
class="add-btn">

    + Add Hotel

</a>

</div>

<table>

<thead>

<tr>

<th>Image</th>

<th>Name</th>

<th>Location</th>

<th>Price / Night</th>

<th>Rooms</th>

<th>Status</th>

<th>Action</th>

</tr>

</thead>

<tbody>

@foreach($hotels as $hotel)

<tr>

<td>

@if($hotel->image)

<img
src="{{ asset('uploads/hotels/'.$hotel->image) }}"
class="table-image">

@else

<img
src="https://via.placeholder.com/100x80"
class="table-image">

@endif

</td>

<td>

{{ $hotel->name }}

</td>

<td>

{{ $hotel->location }}

</td>

<td>

₱{{ number_format(
    $hotel->price_per_night,
    2
) }}

</td>

<td>

{{ $hotel->available_rooms }}

</td>

<td>

@if($hotel->status == 'approved')

<span class="status-approved">

    Approved

</span>

@elseif($hotel->status == 'pending')

<span class="status-pending">

    Pending

</span>

@else

<span class="status-rejected">

    Rejected

</span>

@endif

</td>

<td>

<a
href="/owner/hotels/{{ $hotel->id }}"
class="view-btn">

View

</a>

<a
href="/owner/hotels/edit/{{ $hotel->id }}"
class="edit-btn">

Edit

</a>

<a
href="/owner/hotels/delete/{{ $hotel->id }}"
class="deletes-btn"
onclick="return confirm(
'Are you sure you want to delete this hotel?'
)">

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