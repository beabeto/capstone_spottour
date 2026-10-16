<!DOCTYPE html>
<html>
<head>

<title>Edit Menu</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">

</head>

<body class="owner-listing-page">
@php $workspace = Auth::user()->role === 'admin' ? 'admin' : 'owner'; @endphp

<div class="page-container">

<div class="back-btn">
    <button type="button" onclick="window.history.back(); return false;">Back</button>
</div>

<h1>

 Edit Menu Item

</h1>

<form
method="POST"
action="/owner/menus/update/{{ $menu->id }}"
enctype="multipart/form-data">

@csrf

<label>Menu Name</label>

<input
type="text"
name="name"
value="{{ $menu->name }}"
required>

<label>Category</label>

<select name="category">

<option value="meal"
{{ $menu->category == 'meal' ? 'selected' : '' }}>
Meal
</option>

<option value="drink"
{{ $menu->category == 'drink' ? 'selected' : '' }}>
Drink
</option>

<option value="dessert"
{{ $menu->category == 'dessert' ? 'selected' : '' }}>
Dessert
</option>

<option value="appetizer"
{{ $menu->category == 'appetizer' ? 'selected' : '' }}>
Appetizer
</option>

</select>

<label>Price</label>

<input
type="number"
step="0.01"
name="price"
value="{{ $menu->price }}"
required>

<label>Description</label>

<textarea
name="description"
rows="5">{{ $menu->description }}</textarea>

@if($menu->image)

<img
src="{{ asset('uploads/menus/'.$menu->image) }}"
style="width:150px">

@endif

<label>Replace Image</label>

<input
type="file"
name="image">

<button
type="submit"
class="add-btn">

 Update Menu

</button>

</form>

</div>

</body>
</html>
