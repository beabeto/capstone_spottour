<!DOCTYPE html>
<html>
<head>

<title>Food Menus</title>

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

🍽 {{ $food->name }} Menu

</h1>

<a
href="/owner/foods/{{ $food->id }}/menus/create"
class="add-btn">

+ Add Menu Item

</a>

</div>

@if(session('success'))

<div class="success-box">

    {{ session('success') }}

</div>

@endif

<table>

<thead>

<tr>

<th>Image</th>

<th>Menu Name</th>

<th>Category</th>

<th>Price</th>

<th>Description</th>

<th>Action</th>

</tr>

</thead>

<tbody>

@forelse($menus as $menu)

<tr>

<td>

@if($menu->image)

<img
src="{{ asset('uploads/menus/'.$menu->image) }}"
class="table-image">

@else

No Image

@endif

</td>

<td>

{{ $menu->name }}

</td>

<td>

{{ ucfirst($menu->category) }}

</td>

<td>

₱{{ number_format($menu->price,2) }}

</td>

<td>

{{ $menu->description }}

</td>

<td>

<a href="/owner/menus/edit/{{ $menu->id }}" class="edit-btn">
    Edit
</a>

<a
href="/owner/menus/delete/{{ $menu->id }}"
class="deletes-btn"
onclick="return confirm('Delete this menu item?')">

Delete

</a>

</td>

</tr>

@empty

<tr>

<td colspan="6">

No Menu Items Found

</td>

</tr>

@endforelse

</tbody>

</table>

</div>

</body>

</html>