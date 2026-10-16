<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Food Establishment</title>
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="dashboard-body">
<div class="back-btn"><button type="button" onclick="window.history.back(); return false;">Back</button></div>
<div class="spot-form-container"><div class="spot-form-card">
<h1>Add Food Establishment</h1>
@if($errors->any())
<div class="form-error-box"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif
<form action="{{ Auth::user()->role === 'admin' ? '/admin/foods/store' : '/owner/foods/store' }}" method="POST" enctype="multipart/form-data" class="composed-form">
@csrf
<section class="form-block">
<h2>Restaurant details</h2>
<p>Name, seating, and photos for the restaurant.</p>
@if(Auth::user()->role === 'admin')
<label>Assign to owner
<select name="owner_id" required>
@foreach($owners ?? [] as $owner)
<option value="{{ $owner->id }}" @selected(old('owner_id') == $owner->id)>{{ $owner->name }}</option>
@endforeach
</select>
</label>
@endif
<label>Restaurant name<input type="text" name="name" value="{{ old('name') }}" required></label>
<label>Location<input type="text" name="location" value="{{ old('location') }}" required></label>
<label>Description<textarea name="description" rows="4" required>{{ old('description') }}</textarea></label>
<label>Available tables<input type="number" name="available_tables" min="1" value="{{ old('available_tables') }}" required></label>
<label>Venue capacity<input type="number" name="venue_capacity" min="1" value="{{ old('venue_capacity') }}" placeholder="Total guests the restaurant can serve" required></label>
<label>Price per guest<input type="number" name="price_per_guest" min="0" step="0.01" value="{{ old('price_per_guest') }}" placeholder="Table fee per guest"></label>
<label>Bookings accepted per day<input type="number" name="daily_booking_limit" min="1" value="{{ old('daily_booking_limit') }}" placeholder="Leave blank for no daily limit"></label>
<label>Google Maps link<input type="url" name="map_link" value="{{ old('map_link') }}" placeholder="https://maps.google.com/..."></label>
<label>Restaurant cover image<input type="file" name="image" accept="image/jpeg,image/png,image/webp"></label>
<label>Restaurant gallery<input type="file" name="gallery_images[]" accept="image/jpeg,image/png,image/webp" multiple></label>
</section>
<section class="form-block">
<h2>Menu</h2>
<p>Add the dishes here. You can add more items later from Menus.</p>
<div class="repeat-list" data-repeat>
@foreach(old('menus', [['name' => '', 'category' => 'meal', 'price' => '', 'description' => '']]) as $index => $menu)
<div class="repeat-row" data-row>
<label>Menu name<input type="text" name="menus[{{ $index }}][name]" value="{{ $menu['name'] ?? '' }}" required></label>
<label>Category
<select name="menus[{{ $index }}][category]" required>
@foreach(\App\Models\FoodMenu::categories() as $value => $label)
<option value="{{ $value }}" @selected(($menu['category'] ?? 'meal') === $value)>{{ $label }}</option>
@endforeach
</select>
</label>
<label>Price<input type="number" name="menus[{{ $index }}][price]" min="0" step="0.01" value="{{ $menu['price'] ?? '' }}" required></label>
<label class="wide">Description<textarea name="menus[{{ $index }}][description]" rows="2">{{ $menu['description'] ?? '' }}</textarea></label>
<label class="wide">Menu image<input type="file" name="menus[{{ $index }}][image]" accept="image/jpeg,image/png,image/webp"></label>
<button type="button" class="ghost-btn" data-remove-row>Remove item</button>
</div>
@endforeach
</div>
<div class="repeat-actions"><button type="button" class="ghost-btn" data-add-row>Add another item</button></div>
</section>
<button type="submit" class="form-save-btn">Save restaurant</button>
</form>
</div></div>
<script>
(function () {
    var list = document.querySelector('[data-repeat]');
    var add = document.querySelector('[data-add-row]');
    if (!list || !add) return;
    add.addEventListener('click', function () {
        var rows = list.querySelectorAll('[data-row]');
        var next = rows.length;
        var clone = rows[rows.length - 1].cloneNode(true);
        clone.querySelectorAll('input, select, textarea').forEach(function (field) {
            field.name = field.name.replace(/menus\[\d+\]/, 'menus[' + next + ']');
            if (field.type === 'file') field.value = '';
            else if (field.tagName === 'SELECT') field.selectedIndex = 0;
            else field.value = '';
        });
        list.appendChild(clone);
    });
    list.addEventListener('click', function (event) {
        var button = event.target.closest('[data-remove-row]');
        if (!button || list.querySelectorAll('[data-row]').length < 2) return;
        button.closest('[data-row]').remove();
    });
})();
</script>
</body>
</html>
