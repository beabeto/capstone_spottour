@if($food->menus->isNotEmpty())
    <div class="menu-reserve-block">
        <div class="menu-reserve-head">
            <strong>Reserve menu items</strong>
            <span>Optional — set quantity for dishes you want to pre-order</span>
        </div>
        <div class="menu-reserve-grid">
            @foreach($food->menus as $menu)
                @php
                    $oldQty = (int) old('menu_qty.'.$menu->id, request('menu_qty.'.$menu->id, 0));
                @endphp
                <label class="menu-reserve-card">
                    @if($menu->image)
                        <img src="{{ asset('uploads/menus/'.$menu->image) }}" alt="{{ $menu->name }}">
                    @else
                        <div class="menu-reserve-empty">No photo</div>
                    @endif
                    <div class="menu-reserve-meta">
                        <strong>{{ $menu->name }}</strong>
                        <small>{{ \App\Models\FoodMenu::categories()[$menu->category] ?? ucfirst((string) $menu->category) }}</small>
                        <span>PHP {{ number_format((float) $menu->price, 2) }}</span>
                        <input type="number" name="menu_qty[{{ $menu->id }}]" min="0" max="50" value="{{ $oldQty }}" inputmode="numeric">
                    </div>
                </label>
            @endforeach
        </div>
    </div>
@endif
