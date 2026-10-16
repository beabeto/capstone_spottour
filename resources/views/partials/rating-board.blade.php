@php
    $rows = collect($ratings ?? []);
    $count = $rows->count();
    $average = $count ? (float) $rows->avg('rating') : 0;
@endphp
<section class="owner-rating-board">
    <div class="owner-rating-summary">
        @include('partials.score-stars', ['score' => $average])
        <p>{{ $count }} {{ $count === 1 ? 'guest rating' : 'guest ratings' }}</p>
    </div>
    <div class="owner-rating-bars" aria-label="Rating breakdown">
        @for($star = 5; $star >= 1; $star--)
            @php $bucket = $rows->filter(fn ($row) => (int) round((float) $row->rating) === $star)->count(); @endphp
            <div>
                <span>{{ $star }}</span>
                <i><b style="width: {{ $count ? round($bucket / $count * 100) : 0 }}%"></b></i>
                <small>{{ $bucket }}</small>
            </div>
        @endfor
    </div>
    <ul class="owner-rating-list">
        @forelse($rows->sortByDesc('created_at') as $row)
            <li>
                <img src="{{ $row->user && $row->user->profile_image ? asset('uploads/profile/'.$row->user->profile_image) : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png' }}" alt="">
                <div>
                    <strong>{{ $row->user->name ?? 'Guest' }}</strong>
                    <small>{{ $row->created_at?->diffForHumans() }}</small>
                </div>
                @include('partials.score-stars', ['score' => $row->rating])
            </li>
        @empty
            <li class="owner-rating-empty">No guest ratings yet.</li>
        @endforelse
    </ul>
</section>
