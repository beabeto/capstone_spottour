<!DOCTYPE html>
<html>
<head>

<title>Add Menu Item</title>

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

🍽 Add Menu Item

</h1>

</div>

<form
method="POST"
action="/owner/foods/{{ $food->id }}/menus/store"
enctype="multipart/form-data"
class="form-card">

@csrf

<label>

Menu Name

</label>

<input
type="text"
name="name"
required>

<label>

Category

</label>

<select name="category">

<option value="meal">

Meal

</option>

<option value="drink">

Drink

</option>

<option value="dessert">

Dessert

</option>

<option value="appetizer">

Appetizer

</option>

</select>

<label>

Price

</label>

<input
type="number"
step="0.01"
name="price"
required>

<label>

Description

</label>

<textarea
name="description"
rows="5"></textarea>

<label>

Menu Image

</label>

<input
type="file"
name="image">

<button
type="submit"
class="add-btn">

💾 Save Menu Item

</button>

</form>

</div>

</body>
</html>