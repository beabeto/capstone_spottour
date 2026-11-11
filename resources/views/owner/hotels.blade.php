<!DOCTYPE html>

<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>My Hotels</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">

</head>

<body class="owner-listing-page">

<div class="page-container">

<div class="header-row">

<div class="back-btn">
    <button onclick="window.history.back(); return false;">
         Back
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

<div class="listing-table-scroll"><table>

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
src="{{ \App\Support\ListingImage::url($hotel->image, 'hotels') }}"
class="table-image">

@else

<img
src="{{ asset('images/listing-placeholder.svg') }}"
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

PHP {{ number_format(
    $hotel->price_per_night,
    2
) }}

</td>

<td>

{{ $hotel->available_rooms }}

</td>

<td class="listing-status-cell">
@if($hotel->status == 'approved')
<span class="status-approved">Approved</span>
@elseif($hotel->status == 'pending')
<span class="status-pending">Pending</span>
@else
<span class="status-rejected">Rejected</span>
@endif
</td>

<td>
<div class="listing-action-row listing-action-top">
<a
href="/owner/hotels/{{ $hotel->id }}"
class="view-btn">

View

</a>

<a
href="/owner/hotels/{{ $hotel->id }}/rooms"
class="listing-menu-btn">

Rooms

</a>
</div>

<div class="listing-action-row listing-action-bottom">
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
</div>

</td>

</tr>

@endforeach

</tbody>

</table></div></div>

</div>

</body>

</html>
