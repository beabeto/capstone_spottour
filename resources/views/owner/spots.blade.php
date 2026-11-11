<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>My Spots</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">

</head>
<body class="owner-listing-page">

<div class="page-container">

<div class="header-row">

<div class="back-btn">
    <button type="button" onclick="window.history.back(); return false;">Back</button>
</div>

<h1>My Spots</h1>

<a
href="/owner/spots/create"
class="add-btn">

+ Add Spot

</a>

</div>

<div class="listing-table-scroll"><table>

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
src="{{ \App\Support\ListingImage::url($spot->image, 'spots') }}"
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
PHP {{ $spot->price }}
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

<form method="POST" action="/owner/spots/delete/{{ $spot->id }}" class="listing-delete-form" onsubmit="return confirm('Delete this spot?')">
    @csrf
    <button type="submit" class="deletes-btn">Delete</button>
</form>

</td>

</tr>

@endforeach

</tbody>

</table></div></div>

</div>

</body>
</html>
