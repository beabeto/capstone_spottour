<!DOCTYPE html>
<html>
<body style="margin:0;padding:24px;background:#f3f7f6;font-family:Arial,sans-serif;color:#203531;">
    <div style="max-width:640px;margin:0 auto;background:#fff;border:1px solid #d7e8e3;border-radius:14px;overflow:hidden;">
        <div style="padding:22px 24px;background:#17473f;color:#fff;">
            <div style="font-size:13px;letter-spacing:.08em;text-transform:uppercase;color:#b7e4d6;">SpotTour</div>
            <h1 style="margin:8px 0 0;font-size:22px;">{{ $confirmed ? 'Booking confirmed' : 'Booking received' }}</h1>
        </div>
        <div style="padding:22px 24px;line-height:1.55;">
            <p>Hello {{ $guestName }},</p>
            <p>Your reservation for <strong>{{ $place }}</strong> is {{ $statusPhrase }}</p>
            <p style="margin:18px 0;padding:12px 14px;border-radius:8px;background:#e8f5f1;color:#17473f;">
                Reference <strong>{{ $reference }}</strong>
            </p>
            <table style="width:100%;border-collapse:collapse;">
                @foreach($rows as $label => $value)
                    <tr>
                        <td style="padding:8px 0;border-top:1px solid #e6f0ed;color:#607772;width:38%;">{{ $label }}</td>
                        <td style="padding:8px 0;border-top:1px solid #e6f0ed;font-weight:700;">{{ $value }}</td>
                    </tr>
                @endforeach
            </table>
            <p style="margin-top:18px;">A PDF copy of this confirmation is attached to this email.</p>
            @if($paid && ! $confirmed)
                <p>The listing owner can see your uploaded payment receipt and will check it before approving the booking.</p>
            @endif
        </div>
    </div>
</body>
</html>
