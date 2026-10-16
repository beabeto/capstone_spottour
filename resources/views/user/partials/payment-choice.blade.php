@php
    $selected = old('payment_method', ((float) $amount >= 100 ? 'gcash' : 'cash'));
    $livePay = (bool) config('services.paymongo.live');
    $walletOk = (float) $amount >= 100;
    if (! $walletOk && in_array($selected, ['gcash', 'maya'], true)) {
        $selected = 'cash';
    }
@endphp

@if($errors->any())
    <div class="pay-alert">{{ $errors->first() }}</div>
@endif

@if(! $walletOk)
    <div class="pay-alert is-note">GCash and Maya need a total of at least &#8369;100.00. This booking is &#8369;{{ number_format((float) $amount, 2) }}, so choose cash or bank transfer{{ (float) $amount <= 0 ? ', or ask the owner to set a price' : '' }}.</div>
@endif

<fieldset class="pay-methods">
    <legend>How do you want to pay?</legend>
    <label class="pay-option">
        <input type="radio" name="payment_method" value="cash" @checked($selected === 'cash')>
        <span><strong>Cash</strong><small>Pay at the place. The owner verifies it there.</small></span>
    </label>
    <label class="pay-option {{ $walletOk ? '' : 'is-disabled' }}">
        <input type="radio" name="payment_method" value="gcash" @checked($selected === 'gcash') @disabled(! $walletOk)>
        <span><strong>GCash</strong><small>{{ $walletOk ? ($livePay ? 'Opens the GCash app and charges that account.' : 'Opens GCash through PayMongo.') : 'Needs at least ₱100.00' }}</small></span>
    </label>
    <label class="pay-option {{ $walletOk ? '' : 'is-disabled' }}">
        <input type="radio" name="payment_method" value="maya" @checked($selected === 'maya') @disabled(! $walletOk)>
        <span><strong>Maya</strong><small>{{ $walletOk ? ($livePay ? 'Opens the Maya app and charges that account.' : 'Opens Maya through PayMongo.') : 'Needs at least ₱100.00' }}</small></span>
    </label>
    <label class="pay-option">
        <input type="radio" name="payment_method" value="bank" @checked($selected === 'bank')>
        <span><strong>Bank transfer</strong><small>Upload the deposit slip so the owner can match it.</small></span>
    </label>
</fieldset>
@error('payment_method')<p class="form-error">{{ $message }}</p>@enderror

<div class="pay-panel" data-pay-panel="cash" @if($selected !== 'cash') hidden @endif>
    <strong>Pay in cash when you arrive</strong>
    <p>No online transfer is needed. The owner verifies the cash payment before the reservation is confirmed.</p>
</div>
<div class="pay-panel" data-pay-panel="gcash" @if($selected !== 'gcash') hidden @endif>
    <strong>Continue to GCash for &#8369;{{ number_format((float) $amount, 2) }}</strong>
    <p>{{ $livePay ? 'SpotTour opens the GCash app for this amount. The money is taken from the real GCash account.' : 'The next page is PayMongo. On a phone it opens the GCash app. This test account does not charge real money: choose Authorize on the PayMongo page.' }}</p>
</div>
<div class="pay-panel" data-pay-panel="maya" @if($selected !== 'maya') hidden @endif>
    <strong>Continue to Maya for &#8369;{{ number_format((float) $amount, 2) }}</strong>
    <p>{{ $livePay ? 'SpotTour opens the Maya app for this amount. The money is taken from the real Maya account.' : 'The next page is PayMongo. On a phone it opens the Maya app. This test account does not charge real money: choose Authorize on the PayMongo page.' }}</p>
</div>
<div class="pay-panel" data-pay-panel="bank" @if($selected !== 'bank') hidden @endif>
    <strong>Pay &#8369;{{ number_format((float) $amount, 2) }} by bank transfer</strong>
    <p>Transfer from your bank, then upload the deposit slip and reference number so the owner can verify the payment.</p>
</div>

<div class="pay-proof {{ $selected === 'bank' ? '' : 'is-cash' }}">
    <div class="form-grid">
        <label class="pay-account">Account name<input type="text" name="payer_name" value="{{ old('payer_name') }}" placeholder="Name on the account that paid" autocomplete="name"></label>
        <label class="pay-account">Account or mobile number<input type="text" name="payer_number" value="{{ old('payer_number') }}" placeholder="09XXXXXXXXX or account number"></label>
        <label>Booking email<input type="email" name="contact_email" value="{{ old('contact_email', Auth::user()?->email) }}" placeholder="you@email.com" required></label>
        <label class="pay-account">Reference number<input type="text" name="payment_reference" value="{{ old('payment_reference') }}" placeholder="Reference from the receipt" maxlength="80"></label>
    </div>
    @error('payer_name')<p class="form-error">{{ $message }}</p>@enderror
    @error('payer_number')<p class="form-error">{{ $message }}</p>@enderror
    @error('payment_reference')<p class="form-error">{{ $message }}</p>@enderror
    <label class="receipt-field pay-account">Payment receipt<input type="file" name="receipt" accept="image/jpeg,image/png,image/webp"><span>JPG, PNG, or WEBP, up to 5 MB. The owner uses this to verify the payment.</span></label>
    @error('receipt')<p class="form-error">{{ $message }}</p>@enderror
</div>
<p class="pay-note">{{ $livePay ? 'GCash and Maya charge the real wallet. Cash and bank transfer stay pending until the owner verifies them.' : 'GCash and Maya are confirmed by PayMongo. Cash and bank transfer stay pending until the owner verifies them.' }}</p>

<script>
(function () {
    var form = document.currentScript && document.currentScript.closest('form');
    if (!form) return;

    var panels = form.querySelectorAll('[data-pay-panel]');
    var proof = form.querySelector('.pay-proof');
    var radios = form.querySelectorAll('input[name="payment_method"]');
    var amount = {{ json_encode(number_format((float) $amount, 2, '.', '')) }};

    var labels = {
        cash: 'Submit cash booking',
        gcash: 'Continue to GCash for \u20b1' + amount,
        maya: 'Continue to Maya for \u20b1' + amount,
        bank: 'Submit bank transfer'
    };

    function show(method) {
        panels.forEach(function (panel) {
            panel.hidden = panel.dataset.payPanel !== method;
        });
        if (proof) proof.classList.toggle('is-cash', method !== 'bank');
        form.querySelectorAll('.pay-account input').forEach(function (input) {
            input.required = method === 'bank';
        });
        var button = form.querySelector('.submit-payment-btn');
        if (button) button.textContent = labels[method] || labels.bank;
    }

    radios.forEach(function (radio) {
        radio.addEventListener('change', function () {
            if (radio.checked) show(radio.value);
        });
        if (radio.checked) show(radio.value);
    });
})();
</script>
