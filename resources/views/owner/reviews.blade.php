<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest ratings | SpotTour</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        .owner-rating-page{background:#f3f7f6}
        .owner-rating-page .page-container{width:min(980px,calc(100% - 40px))!important;margin:0 auto!important;padding:78px 0 56px!important}
        .owner-rating-page .management-heading{margin:0 0 16px!important}
        .rating-toolbar{display:grid;grid-template-columns:1.2fr .8fr .8fr;gap:12px;margin:0 0 16px;padding:14px;border:1px solid #d7e8e3;border-radius:14px;background:#fff}
        .rating-toolbar label{display:grid;gap:6px;color:#48635c;font-size:12px;font-weight:800}
        .rating-toolbar input,.rating-toolbar select{min-height:42px;padding:0 12px;border:1px solid #d5ebe4;border-radius:10px;background:#fff;color:#203531;font:inherit}
        .rating-summary-pills{display:flex;flex-wrap:wrap;gap:8px;margin:0 0 14px}
        .rating-summary-pills span{display:inline-flex;align-items:center;min-height:30px;padding:0 10px;border-radius:999px;background:#e8f5f1;color:#126a59;font-size:12px;font-weight:800}
        .owner-rating-overview{display:flex;align-items:center;justify-content:space-between;gap:16px;margin:0 0 14px;padding:16px 18px;border:1px solid #d7e8e3;border-radius:14px;background:#fff}
        .owner-rating-overview p{margin:0;color:#607772;font-size:13px}
        .rating-accordion{margin:0 0 12px;border:1px solid #d7e8e3;border-radius:14px;background:#fff;overflow:hidden}
        .rating-accordion[hidden]{display:none!important}
        .rating-accordion > summary{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 16px;list-style:none;cursor:pointer;background:#f6fbf9}
        .rating-accordion > summary::-webkit-details-marker{display:none}
        .rating-accordion > summary h2{margin:0;color:#17473f;font-size:16px}
        .rating-accordion > summary small{color:#607772;font-size:12px;font-weight:700}
        .rating-accordion > summary .count{display:inline-flex;align-items:center;justify-content:center;min-width:28px;min-height:28px;padding:0 8px;border-radius:999px;background:#167b69;color:#fff;font-size:12px;font-weight:800}
        .rating-accordion-body{display:grid;gap:12px;padding:12px 14px 14px;border-top:1px solid #e7f0ed}
        .owner-rating-card{margin:0;padding:14px;border:1px solid #e7f0ed;border-radius:12px;background:#fbfefd}
        .owner-rating-card[hidden]{display:none!important}
        .owner-rating-card header{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:12px}
        .owner-rating-card header span{color:#167b69;font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase}
        .owner-rating-card h3{margin:4px 0 0;color:#17473f;font-size:17px}
        .owner-rating-card header a{color:#167b69;font-size:13px;font-weight:800;text-decoration:none;white-space:nowrap}
        .rating-empty,.rating-filter-empty{padding:22px 16px;border:1px dashed #c9e2da;border-radius:14px;background:#fff;color:#607772;text-align:center}
        .rating-filter-empty[hidden]{display:none!important}
        @media(max-width:760px){
            .owner-rating-page .page-container{width:min(100% - 24px,980px)!important;padding-top:70px!important}
            .rating-toolbar{grid-template-columns:1fr}
            .owner-rating-overview{flex-direction:column;align-items:flex-start}
        }
    </style>
</head>
<body class="owner-body owner-rating-page">
@php
    $allRatings = $listings->flatMap->ratings;
    $ratedListings = $listings->filter(fn ($listing) => $listing->ratings->isNotEmpty());
    $groups = [
        'spot' => ['label' => 'Tourist spots', 'items' => $listings->where('kind', 'spot')->values()],
        'hotel' => ['label' => 'Hotels', 'items' => $listings->where('kind', 'hotel')->values()],
        'food' => ['label' => 'Restaurants', 'items' => $listings->where('kind', 'food')->values()],
    ];
@endphp
<div class="back-btn"><button type="button" onclick="window.history.back(); return false;">Back</button></div>
<main class="page-container">
    <header class="management-heading">
        <span>Owner workspace</span>
        <h1>Guest ratings</h1>
        <p>See the star ratings guests gave each listing. Filter by type or open only the section you need.</p>
    </header>

    <div class="rating-toolbar">
        <label>Search listing
            <input type="search" id="ratingSearch" placeholder="Listing name">
        </label>
        <label>Type
            <select id="ratingType">
                <option value="all">All types</option>
                <option value="spot">Tourist spots</option>
                <option value="hotel">Hotels</option>
                <option value="food">Restaurants</option>
            </select>
        </label>
        <label>Show
            <select id="ratingShow">
                <option value="rated">With ratings</option>
                <option value="all">All listings</option>
                <option value="empty">No ratings yet</option>
            </select>
        </label>
    </div>

    <div class="rating-summary-pills">
        <span>{{ $allRatings->count() }} total ratings</span>
        <span>{{ $ratedListings->count() }} rated listings</span>
        <span>{{ $listings->count() }} listings overall</span>
    </div>

    @if($allRatings->isNotEmpty())
        <section class="owner-rating-overview">
            @include('partials.score-stars', ['score' => $allRatings->avg('rating')])
            <p>Average across all guest ratings on your listings.</p>
        </section>
    @endif

    @forelse($groups as $kind => $group)
        @php
            $groupRated = $group['items']->filter(fn ($listing) => $listing->ratings->isNotEmpty())->count();
            $groupRatings = $group['items']->sum(fn ($listing) => $listing->ratings->count());
        @endphp
        <details class="rating-accordion" data-section="{{ $kind }}" {{ $groupRated > 0 ? 'open' : '' }}>
            <summary>
                <div>
                    <h2>{{ $group['label'] }}</h2>
                    <small>{{ $groupRated }} rated · {{ $groupRatings }} ratings · {{ $group['items']->count() }} listings</small>
                </div>
                <span class="count">{{ $group['items']->count() }}</span>
            </summary>
            <div class="rating-accordion-body">
                @forelse($group['items'] as $listing)
                    <article class="owner-rating-card"
                             data-kind="{{ $kind }}"
                             data-rated="{{ $listing->ratings->isNotEmpty() ? '1' : '0' }}"
                             data-search="{{ strtolower($listing->name) }}">
                        <header>
                            <div>
                                <span>{{ $listing->type }}</span>
                                <h3>{{ $listing->name }}</h3>
                            </div>
                            <a href="{{ $listing->url }}">Open listing</a>
                        </header>
                        @include('partials.rating-board', ['ratings' => $listing->ratings])
                    </article>
                @empty
                    <div class="rating-empty">No {{ strtolower($group['label']) }} yet.</div>
                @endforelse
                <div class="rating-filter-empty" hidden>No listings match these filters.</div>
            </div>
        </details>
    @empty
        <div class="rating-empty">No listings yet.</div>
    @endforelse
</main>
<script>
(function () {
    var search = document.getElementById('ratingSearch');
    var type = document.getElementById('ratingType');
    var show = document.getElementById('ratingShow');
    var sections = Array.prototype.slice.call(document.querySelectorAll('.rating-accordion'));

    function applyFilters() {
        var query = (search.value || '').trim().toLowerCase();
        var selected = type.value;
        var mode = show.value;

        sections.forEach(function (section) {
            var sectionKind = section.getAttribute('data-section');
            var showSection = selected === 'all' || selected === sectionKind;
            section.hidden = !showSection;
            if (!showSection) return;

            var visible = 0;
            section.querySelectorAll('.owner-rating-card').forEach(function (card) {
                var rated = card.getAttribute('data-rated') === '1';
                var modeOk = mode === 'all' || (mode === 'rated' && rated) || (mode === 'empty' && !rated);
                var searchOk = !query || (card.getAttribute('data-search') || '').indexOf(query) !== -1;
                var ok = modeOk && searchOk;
                card.hidden = !ok;
                if (ok) visible += 1;
            });

            var empty = section.querySelector('.rating-filter-empty');
            if (empty) empty.hidden = visible > 0;
            if (visible > 0 && mode === 'rated') section.open = true;
        });
    }

    [search, type, show].forEach(function (el) {
        if (!el) return;
        el.addEventListener(el.tagName === 'INPUT' ? 'input' : 'change', applyFilters);
    });
    applyFilters();
})();
</script>
</body>
</html>
