<?php

namespace App\Support;

use App\Models\Booking;
use App\Models\Food;
use App\Models\FoodBooking;
use App\Models\Hotel;
use App\Models\HotelBooking;
use App\Models\Spot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class TravellerAssistant
{
    public function respond(User $user, string $text): array
    {
        $lower = mb_strtolower(trim($text));
        $filipino = $this->isFilipino($lower);

        if ($this->wantsHelper($lower)) {
            return $this->pack($filipino
                ? 'Sige, ako na ulit. Tanong ka lang tungkol sa booking, lugar, o reservation mo.'
                : 'I can take questions again. Ask about booking, a place, or your reservations.', null);
        }

        if ($this->wantsAdmin($lower)) {
            return $this->pack($filipino
                ? 'Ipapasa ko ito sa admin. May notification sila, at dito rin sila sasagot. I-type ang “back to the helper” kung gusto mo ulit akong sumagot.'
                : 'I am passing this to an admin. They will be notified and can reply here. Type “back to the helper” if you want me to answer again.', 'admin');
        }

        $listing = $this->mentionedListing($lower);

        if ($this->wantsOwner($lower)) {
            if (!$listing) {
                return $this->pack($filipino
                    ? 'Sino ang owner na kakausapin? Isulat ang pangalan ng hotel, spot, o restaurant, halimbawa “owner of Philippine Gateway Hotel”.'
                    : 'Which owner should I call? Type the place name, for example “owner of Philippine Gateway Hotel”.', null);
            }

            return $this->pack($filipino
                ? 'Ipapasa ko ito sa owner ng '.$listing['name'].'. Manonotify sila at dito sasagot. Puwede pa ring magtanong sa admin kung hindi sila makasagot.'
                : 'I am passing this to the owner of '.$listing['name'].'. They will be notified and can reply here. You can still ask an admin if you need SpotTour itself.', 'owner', $listing);
        }

        if ($this->asksBookings($lower)) {
            return $this->pack($this->bookingsAnswer($user, $filipino), null, $listing);
        }

        if ($this->asksPayment($lower)) {
            return $this->pack($filipino
                ? 'Sa checkout, pumili ng Cash, GCash, Maya, o bank. Ang GCash at Maya ay online checkout. Ang cash at bank ay kinukumpirma ng owner pagkatapos mong mag-submit.'
                : 'At checkout you can pay by cash, GCash, Maya, or bank. GCash and Maya open an online checkout. Cash and bank are confirmed by the owner after you submit.', null, $listing);
        }

        if ($this->asksHowToBook($lower)) {
            return $this->pack($this->howToBook($lower, $filipino), null, $listing);
        }

        if ($this->asksFull($lower)) {
            return $this->pack($filipino
                ? 'Kapag puno na ang araw, Full ang lalabas sa calendar ng booking. Ibig sabihin naabot na ang daily limit ng owner. Pumili ng ibang araw.'
                : 'A date shows Full on the booking calendar when that place has reached the owner’s daily limit. Choose another day.', null, $listing);
        }

        if ($this->asksPassword($lower)) {
            return $this->pack($filipino
                ? 'Buksan ang Profile, tapos Change password. Magse-send ang SpotTour ng code sa email mo bago mapalitan ang password.'
                : 'Open Profile, then Change password. SpotTour emails you a code before the password can be changed.', null);
        }

        $found = $this->searchPlaces($lower);
        if ($found !== []) {
            $lines = array_map(fn ($place) => $place['line'], $found);
            $intro = $filipino ? 'Ito ang malapit sa tinanong mo:' : 'Here is what matches your question:';

            return $this->pack($intro."\n".$this->lines($lines), null, $found[0]['listing']);
        }

        if ($this->isGreeting($lower)) {
            return $this->pack($filipino
                ? 'Kumusta. Puwede kitang tulungan mag-book, maghanap ng lugar, o tingnan ang reservations mo. Kung tao ang kailangan, sabihin lang na i-refer sa admin o sa owner.'
                : 'Hi. I can help you book, find a place, or check your reservations. If you need a person, say you want an admin or the owner.', null);
        }

        return $this->pack($filipino
            ? 'Hindi ko sigurado iyan. Subukan ang “how do I book”, “my bookings”, o ang pangalan ng lugar. Puwede rin kitang i-refer sa admin o sa owner ng isang listing.'
            : 'I am not sure about that. Try “how do I book”, “my bookings”, or the name of a place. I can also refer you to an admin or to a listing owner.', null, $listing);
    }

    private function pack(string $text, ?string $refer, ?array $listing = null): array
    {
        return ['text' => $text, 'refer' => $refer, 'listing' => $listing];
    }

    private function isFilipino(string $text): bool
    {
        return (bool) preg_match('/\b(paano|saan|magkano|pwede|puwede|hindi|tulong|kumusta|ano|bakit|gusto|may-ari|ako|naman|po|ba|yung|lang)\b/u', $text);
    }

    private function isGreeting(string $text): bool
    {
        return (bool) preg_match('/\b(hi|hello|hey|kumusta|good morning|good evening)\b/u', $text);
    }

    private function wantsHelper(string $text): bool
    {
        return (bool) preg_match('/back to the helper|chat ?bot|helper na/u', $text);
    }

    private function wantsAdmin(string $text): bool
    {
        return (bool) preg_match('/\b(admin|staff|administrator)\b|kausapin ang admin|i-?refer sa admin|talk to (an )?admin|need a person/u', $text);
    }

    private function wantsOwner(string $text): bool
    {
        return (bool) preg_match('/\b(owner|may-ari)\b|kausapin ang owner|i-?refer sa owner|talk to (the )?owner/u', $text);
    }

    private function asksBookings(string $text): bool
    {
        return (bool) preg_match('/my bookings?|show my bookings?|my reservations?|bookings ko|reservation ko|nasaan ang booking|status ng/u', $text);
    }

    private function asksPayment(string $text): bool
    {
        return (bool) preg_match('/\b(pay|payment|gcash|maya|cash|bank|bayad)\b/u', $text);
    }

    private function asksHowToBook(string $text): bool
    {
        return (bool) preg_match('/how (do|to)|paano|book a|mag-?book|reserve/u', $text);
    }

    private function asksFull(string $text): bool
    {
        return (bool) preg_match('/\b(full|fully booked|puno)\b/u', $text);
    }

    private function asksPassword(string $text): bool
    {
        return (bool) preg_match('/password|kalimutan/u', $text);
    }

    private function howToBook(string $text, bool $filipino): string
    {
        if (str_contains($text, 'hotel') || str_contains($text, 'room') || str_contains($text, 'stay')) {
            return $filipino
                ? "Para mag-book ng hotel:\n1. Buksan ang Hotels at piliin ang place.\n2. Piliin ang room type, check-in, at check-out.\n3. Magpatuloy sa payment.\nAng room type ay may pax, gaya ng Deluxe Room (2 pax)."
                : "To book a hotel:\n1. Open Hotels and choose a place.\n2. Pick the room type, check-in, and check-out.\n3. Continue to payment.\nRoom types include how many guests fit, such as Deluxe Room (2 pax).";
        }

        if (str_contains($text, 'food') || str_contains($text, 'restaurant') || str_contains($text, 'kain') || str_contains($text, 'menu')) {
            return $filipino
                ? "Para mag-reserve ng restaurant:\n1. Buksan ang Restaurants at piliin ang place.\n2. Piliin ang petsa, oras, at bilang ng guests.\n3. Magpatuloy sa payment.\nMakikita ang menu sa page ng restaurant."
                : "To reserve a restaurant:\n1. Open Restaurants and choose a place.\n2. Pick the date, time, and number of guests.\n3. Continue to payment.\nThe menu is on the restaurant page.";
        }

        return $filipino
            ? "Para mag-book:\n1. Buksan ang Spots, Hotels, o Restaurants.\n2. Piliin ang place at ang petsa.\n3. Magpatuloy sa payment.\nSabihin kung hotel, spot, o restaurant ang gusto mo at gagabayan kita."
            : "To book:\n1. Open Spots, Hotels, or Restaurants.\n2. Choose a place and a date.\n3. Continue to payment.\nTell me if you want a spot, hotel, or restaurant and I will walk you through that one.";
    }

    private function bookingsAnswer(User $user, bool $filipino): string
    {
        $lines = [];

        foreach (Booking::with('spot')->where('user_id', $user->id)->latest()->take(3)->get() as $booking) {
            $lines[] = 'Spot · '.(optional($booking->spot)->name ?? 'Removed listing').' · '.$this->date($booking->booking_date).' · '.$this->status($booking->status);
        }

        foreach (HotelBooking::with('hotel')->where('user_id', $user->id)->latest()->take(3)->get() as $booking) {
            $lines[] = 'Hotel · '.(optional($booking->hotel)->name ?? 'Removed listing').' · '.$this->date($booking->check_in).' · '.$this->status($booking->status);
        }

        foreach (FoodBooking::with('food')->where('user_id', $user->id)->latest()->take(3)->get() as $booking) {
            $lines[] = 'Restaurant · '.(optional($booking->food)->name ?? 'Removed listing').' · '.$this->date($booking->reservation_date).' · '.$this->status($booking->status);
        }

        if ($lines === []) {
            return $filipino
                ? 'Wala ka pang reservation. Buksan ang Spots, Hotels, o Restaurants, tapos Book.'
                : 'You do not have a reservation yet. Open Spots, Hotels, or Restaurants, then Book.';
        }

        $intro = $filipino ? 'Ito ang mga reservation mo:' : 'Here are your reservations:';

        return $intro."\n".$this->lines(array_slice($lines, 0, 6));
    }

    private function searchPlaces(string $text): array
    {
        $words = $this->keywords($text);
        $generic = ['hotel', 'hotels', 'spot', 'spots', 'food', 'restaurant', 'restaurants', 'room', 'rooms', 'place', 'places', 'surigao', 'city', 'booking', 'bookings'];
        $specific = array_values(array_filter($words, fn ($word) => !in_array($word, $generic, true)));
        $words = $specific !== [] ? $specific : $words;
        if ($words === []) {
            return [];
        }

        $places = [];

        foreach (Spot::where('status', 'approved')->get(['id', 'name', 'location', 'price', 'user_id']) as $spot) {
            if ($this->matches($spot->name.' '.$spot->location, $words)) {
                $places[] = [
                    'line' => $spot->name.' · spot · '.($spot->location ?: 'Surigao').' · PHP '.number_format((float) $spot->price, 0),
                    'listing' => ['type' => 'spot', 'id' => $spot->id, 'owner_id' => $spot->user_id, 'name' => $spot->name],
                ];
            }
        }

        foreach (Hotel::where('status', 'approved')->get(['id', 'name', 'location', 'price_per_night', 'owner_id']) as $hotel) {
            if ($this->matches($hotel->name.' '.$hotel->location, $words)) {
                $places[] = [
                    'line' => $hotel->name.' · hotel · '.($hotel->location ?: 'Surigao').' · PHP '.number_format((float) $hotel->price_per_night, 0).' / night',
                    'listing' => ['type' => 'hotel', 'id' => $hotel->id, 'owner_id' => $hotel->owner_id, 'name' => $hotel->name],
                ];
            }
        }

        foreach (Food::where('status', 'approved')->get(['id', 'name', 'location', 'owner_id']) as $food) {
            if ($this->matches($food->name.' '.$food->location, $words)) {
                $places[] = [
                    'line' => $food->name.' · restaurant · '.($food->location ?: 'Surigao'),
                    'listing' => ['type' => 'food', 'id' => $food->id, 'owner_id' => $food->owner_id, 'name' => $food->name],
                ];
            }
        }

        return array_slice($places, 0, 4);
    }

    private function mentionedListing(string $text): ?array
    {
        $found = $this->searchPlaces($text);

        return $found[0]['listing'] ?? null;
    }

    private function keywords(string $text): array
    {
        $stop = ['the', 'a', 'an', 'to', 'how', 'do', 'i', 'my', 'can', 'where', 'what', 'is', 'are', 'me', 'of', 'for', 'in', 'on', 'please', 'show', 'find', 'looking', 'want', 'need', 'about', 'this', 'that', 'with', 'and', 'or', 'sa', 'ang', 'mga', 'ba', 'po', 'paano', 'saan', 'ako', 'ng', 'na', 'yung', 'ung', 'lang', 'naman', 'pwede', 'puwede', 'mag', 'ano', 'may', 'mga'];
        $words = preg_split('/[^\p{L}\p{N}]+/u', $text) ?: [];

        return array_values(array_unique(array_filter($words, fn ($word) => mb_strlen($word) >= 4 && !in_array($word, $stop, true))));
    }

    private function matches(string $haystack, array $words): bool
    {
        $haystack = mb_strtolower($haystack);
        foreach ($words as $word) {
            if (str_contains($haystack, $word)) {
                return true;
            }
        }

        return false;
    }

    private function lines(array $lines): string
    {
        return implode("\n", array_map(fn ($line) => '• '.$line, $lines));
    }

    private function date($value): string
    {
        if (!$value) {
            return 'date not set';
        }

        return Carbon::parse($value)->format('M j, Y');
    }

    private function status(?string $status): string
    {
        return Str::of($status ?: 'pending')->replace('_', ' ')->title()->toString();
    }
}
