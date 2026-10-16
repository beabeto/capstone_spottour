<?php

namespace App\Support;

use App\Models\Food;
use App\Models\Hotel;
use App\Models\Spot;

class PlaceMap
{
    public static function coordinates(mixed $latitude, mixed $longitude, ?string $mapLink): ?array
    {
        if (is_numeric($latitude) && is_numeric($longitude)) {
            $lat = (float) $latitude;
            $lng = (float) $longitude;

            if (abs($lat) <= 90 && abs($lng) <= 180 && ! ($lat === 0.0 && $lng === 0.0)) {
                return [$lat, $lng];
            }
        }

        $url = trim((string) $mapLink);

        if ($url === '') {
            return null;
        }

        if (preg_match('/!3d(-?\d+(?:\.\d+)?)!4d(-?\d+(?:\.\d+)?)/', $url, $pin)) {
            return [(float) $pin[1], (float) $pin[2]];
        }

        if (preg_match('/[?&](?:q|query|ll)=(-?\d+(?:\.\d+)?),\s*(-?\d+(?:\.\d+)?)/', $url, $pin)) {
            return [(float) $pin[1], (float) $pin[2]];
        }

        if (preg_match('/@(-?\d+(?:\.\d+)?),\s*(-?\d+(?:\.\d+)?),(\d+(?:\.\d+)?)z/', $url, $pin)) {
            if ((float) $pin[3] >= 16) {
                return [(float) $pin[1], (float) $pin[2]];
            }

            return null;
        }

        if (preg_match('/@(-?\d+(?:\.\d+)?),\s*(-?\d+(?:\.\d+)?)/', $url, $pin)) {
            return [(float) $pin[1], (float) $pin[2]];
        }

        return null;
    }

    public static function locate(mixed $latitude, mixed $longitude, ?string $mapLink, string $name = '', string $location = ''): array
    {
        $coords = self::coordinates($latitude, $longitude, $mapLink)
            ?? self::geocode(trim($name.' '.$location));

        return [
            'latitude' => $coords[0] ?? null,
            'longitude' => $coords[1] ?? null,
        ];
    }

    public static function pins(): array
    {
        $rows = [];

        foreach (Spot::where('status', 'approved')->get() as $spot) {
            $rows[] = ['spot', 'Spot', $spot->name, $spot->location, $spot->latitude, $spot->longitude, $spot->map_link, '/spots/'.$spot->id];
        }

        foreach (Hotel::where('status', 'approved')->get() as $hotel) {
            $rows[] = ['hotel', 'Hotel', $hotel->name, $hotel->location, $hotel->latitude, $hotel->longitude, $hotel->map_link, '/hotels/'.$hotel->id];
        }

        foreach (Food::where('status', 'approved')->get() as $food) {
            $rows[] = ['restaurant', 'Restaurant', $food->name, $food->location, $food->latitude, $food->longitude, $food->map_link, '/foods/'.$food->id];
        }

        $shared = [];

        foreach ($rows as $row) {
            $coords = self::coordinates($row[4], $row[5], $row[6]);
            $key = self::locationKey($row[3]);

            if ($coords && $key !== '') {
                $shared[$key] = $coords;
            }
        }

        $pins = [];

        foreach ($rows as $row) {
            $coords = self::coordinates($row[4], $row[5], $row[6])
                ?? ($shared[self::locationKey($row[3])] ?? null);

            $pins[] = self::pin($row[0], $row[1], $row[2], $row[3], $coords[0] ?? null, $coords[1] ?? null, $row[6], $row[7]);
        }

        return self::spread(array_values(array_filter($pins)));
    }

    private static function locationKey(?string $location): string
    {
        return strtolower(trim((string) $location));
    }

    public static function remember(Spot|Hotel|Food $place): void
    {
        $coords = self::locate(
            $place->latitude,
            $place->longitude,
            $place->map_link,
            (string) $place->name,
            (string) $place->location
        );

        if ($coords['latitude'] === null) {
            return;
        }

        $place->latitude = $coords['latitude'];
        $place->longitude = $coords['longitude'];
        $place->save();
    }

    private static function pin(string $type, string $label, ?string $name, ?string $location, mixed $latitude, mixed $longitude, ?string $mapLink, string $url): ?array
    {
        $coords = self::coordinates($latitude, $longitude, $mapLink);

        if (! $coords) {
            return null;
        }

        $link = trim((string) $mapLink);

        if ($link !== '' && ! str_starts_with($link, 'http')) {
            $link = 'https://'.$link;
        }

        if (! filter_var($link, FILTER_VALIDATE_URL)) {
            $link = 'https://www.google.com/maps/search/?api=1&query='.$coords[0].','.$coords[1];
        }

        return [
            'type' => $type,
            'label' => $label,
            'name' => $name,
            'location' => $location,
            'lat' => $coords[0],
            'lng' => $coords[1],
            'url' => $url,
            'maps' => $link,
        ];
    }

    private static function spread(array $pins): array
    {
        $groups = [];

        foreach ($pins as $index => $pin) {
            $key = round($pin['lat'], 4).'|'.round($pin['lng'], 4);
            $groups[$key][] = $index;
        }

        foreach ($groups as $indexes) {
            if (count($indexes) < 2) {
                continue;
            }

            foreach ($indexes as $step => $index) {
                $angle = (2 * M_PI * $step) / count($indexes);
                $pins[$index]['lat'] += 0.00045 * cos($angle);
                $pins[$index]['lng'] += 0.00045 * sin($angle);
            }
        }

        return $pins;
    }

    private static function geocode(string $query): ?array
    {
        $query = trim($query);

        if ($query === '' || ! function_exists('curl_init')) {
            return null;
        }

        $url = 'https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&countrycodes=ph&q='.rawurlencode($query.' Surigao City Philippines');
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 6,
            CURLOPT_HTTPHEADER => [
                'User-Agent: SpotTour/1.0 (Surigao City listings)',
                'Accept: application/json',
            ],
        ]);

        $body = curl_exec($curl);
        curl_close($curl);
        $rows = json_decode((string) $body, true);

        if (! is_array($rows) || ! isset($rows[0]['lat'], $rows[0]['lon'])) {
            return null;
        }

        return [(float) $rows[0]['lat'], (float) $rows[0]['lon']];
    }
}
