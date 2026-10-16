@php
    $inputId = $inputId ?? 'star';
    $selected = (int) ($value ?? 0);
@endphp
<div class="star-rating {{ !empty($small) ? 'star-rating-small' : '' }}" role="radiogroup" aria-label="{{ $label ?? 'Rating' }}">
    @for($rating = 5; $rating >= 1; $rating--)
        <input id="{{ $inputId }}-{{ $rating }}" type="radio" name="rating" value="{{ $rating }}" @checked($selected === $rating) onchange="this.form.submit()">
        <label for="{{ $inputId }}-{{ $rating }}" class="{{ $selected === $rating ? 'is-on' : '' }}" title="{{ $rating }} {{ $rating === 1 ? 'star' : 'stars' }}">&#9733;</label>
    @endfor
</div>
