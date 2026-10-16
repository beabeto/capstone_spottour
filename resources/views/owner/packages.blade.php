<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Packages | SpotTour</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/listing-details.css') }}">
</head>
<body class="dashboard-body booking-form-page">
<div class="back-btn"><button type="button" onclick="window.history.back()">Back</button></div>
<main class="booking-form-shell">
    <section class="booking-form-heading">
        <span>What you offer</span>
        <h1>Packages</h1>
        <p>Spots can offer a tourist guide. Hotels can offer services. Restaurants can offer occasions such as a wedding.</p>
    </section>
    @if(session('success'))<div class="toast-success">{{ session('success') }}</div>@endif
    <form method="POST" action="/owner/packages" class="spot-card">
        @csrf
        <label>Listing
            <select name="place" required>
                <option value="">Choose a place</option>
                @foreach($spots as $spot)<option value="spot:{{ $spot->id }}">Spot — {{ $spot->name }}</option>@endforeach
                @foreach($hotels as $hotel)<option value="hotel:{{ $hotel->id }}">Hotel — {{ $hotel->name }}</option>@endforeach
                @foreach($foods as $food)<option value="food:{{ $food->id }}">Restaurant — {{ $food->name }}</option>@endforeach
            </select>
        </label>
        <label>Package name<input type="text" name="name" placeholder="Tourist guide, airport pickup, wedding reception" required></label>
        <label>Type
            <select name="kind" required>
                <option value="guide">Tourist guide</option>
                <option value="service">Hotel service</option>
                <option value="event">Restaurant occasion</option>
            </select>
        </label>
        <label>Details<textarea name="description" placeholder="What is included"></textarea></label>
        <label>Price (optional)<input type="number" name="price" min="0" step="0.01" placeholder="Leave blank if included"></label>
        <button type="submit" class="submit-payment-btn">Save package</button>
    </form>
    <section class="spot-card">
        <h2>Your packages</h2>
        @php $packages = $spots->flatMap->packages->merge($hotels->flatMap->packages)->merge($foods->flatMap->packages); @endphp
        @forelse($packages as $package)
            <p><strong>{{ $package->name }}</strong> — {{ $package->placeName() }}
                @if($package->price) · PHP {{ number_format($package->price, 2) }}@endif
                <a href="/owner/packages/delete/{{ $package->id }}" onclick="return confirm('Remove this package?')">Remove</a>
            </p>
        @empty
            <p>No packages yet.</p>
        @endforelse
    </section>
</main>
</body>
</html>
