<!DOCTYPE html>

<html>
<head>

<title>Add Food Establishment</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">

</head>

<body class="dashboard-body">

<div class="back-btn">

    <button onclick="history.back()">

        ←

    </button>

</div>

<div class="form-container">

<h1>

🍽️ Add Food Establishment

</h1>

<form
action="/owner/foods/store"
method="POST"
enctype="multipart/form-data">

@csrf

<input
type="text"
name="name"
placeholder="Restaurant Name"
required>

<input
type="text"
name="location"
placeholder="Location"
required>

<textarea
name="description"
placeholder="Description"
required>
</textarea>

<input
type="number"
name="available_tables"
placeholder="Available Tables">

<input
type="number"
name="venue_capacity"
placeholder="Venue Capacity">

<input
type="text"
name="map_link"
placeholder="Google Map Link">

<input
type="file"
name="image">

<button
type="submit">

    Save Food Establishment

</button>

</form>

</div>

</body>

</html>