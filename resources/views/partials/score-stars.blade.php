@php
    $score = (float) ($score ?? 0);
    $filled = (int) round($score);
@endphp
<span class="score-stars" aria-label="{{ number_format($score, 1) }} out of 5">
    @for($star = 1; $star <= 5; $star++)
        <i class="{{ $star <= $filled ? 'is-on' : '' }}">&#9733;</i>
    @endfor
    <b>{{ number_format($score, 1) }}</b>
</span>
