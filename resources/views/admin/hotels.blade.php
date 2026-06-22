<!DOCTYPE html>

<html>
<head>

<title>Hotel Management</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">

</head>

<body class="dashboard-body">

<div class="page-container">

<div class="header-row">

<div class="back-btn">
    <button onclick="history.back()">
        ← 
    </button>
</div>

🏨 Hotel Management

</h1>

</div>

@if(session('success'))

<div class="success">

    {{ session('success') }}

</div>

@endif

<table>

<thead>

<tr>

<th>Image</th>

<th>Name</th>

<th>Location</th>

<th>Price/Night</th>

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
href="/admin/hotels/{{ $hotel->id }}"
class="view-btn">

View

</a>

<a
href="/admin/hotels/approve/{{ $hotel->id }}"
class="approve-btn">

Approve

</a>

<a
href="/admin/hotels/reject/{{ $hotel->id }}"
class="reject-btn">

Reject

</a>

<a
href="/admin/hotels/delete/{{ $hotel->id }}"
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