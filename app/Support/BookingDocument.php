<?php

namespace App\Support;

class BookingDocument
{
    public static function pdf(string $title, array $rows): string
    {
        $reference = self::plain((string) ($rows['Reference'] ?? ''));
        $place = self::plain((string) ($rows['Place'] ?? $title));
        $status = self::plain((string) ($rows['Status'] ?? ''));
        $total = self::plain((string) ($rows['Total'] ?? ''));
        $confirmed = str_contains($status, 'Confirmed');
        $details = array_diff_key($rows, array_flip(['Reference', 'Place', 'Status', 'Total']));

        $commands = [];
        $commands[] = self::fill(0.933, 0.957, 0.949, 0, 0, 595, 842);
        $commands[] = self::fill(0.090, 0.278, 0.247, 0, 748, 595, 94);
        $commands[] = self::fill(0.910, 0.722, 0.329, 0, 748, 595, 4);
        $commands[] = self::text(36, 802, 22, 'F2', 'SpotTour', 1, 1, 1);
        $commands[] = self::text(36, 780, 10, 'F1', 'Official booking record', 0.780, 0.910, 0.870);
        if ($reference !== '') {
            $commands[] = self::text(430, 808, 8, 'F1', 'REFERENCE', 0.780, 0.910, 0.870);
            $commands[] = self::text(430, 788, 14, 'F2', $reference, 1, 1, 1);
        }

        $commands[] = self::fill(1, 1, 1, 28, 36, 539, 688);
        $commands[] = self::stroke(0.820, 0.890, 0.870, 28, 36, 539, 688);

        $placeY = 684;
        foreach (self::wrap($place, 34) as $line) {
            $commands[] = self::text(48, $placeY, 18, 'F2', $line, 0.090, 0.278, 0.247);
            $placeY -= 22;
        }

        $statusColor = $confirmed ? [0.071, 0.416, 0.349] : [0.604, 0.392, 0.071];
        $commands[] = self::fill($confirmed ? 0.906 : 1, $confirmed ? 0.961 : 0.965, $confirmed ? 0.941 : 0.894, 48, $placeY - 8, 220, 22);
        $commands[] = self::text(58, $placeY - 1, 10, 'F2', $status !== '' ? $status : 'Pending', ...$statusColor);

        $y = $placeY - 42;
        $commands[] = self::text(48, $y, 9, 'F2', 'RESERVATION DETAILS', 0.376, 0.510, 0.478);
        $y -= 16;

        foreach ($details as $label => $value) {
            $valueLines = self::wrap(self::plain((string) $value), 42);
            $commands[] = self::fill(0.890, 0.925, 0.910, 48, $y + 12, 499, 0.6);
            $commands[] = self::text(48, $y, 10, 'F1', self::plain((string) $label), 0.376, 0.510, 0.478);
            $lineY = $y;
            foreach ($valueLines as $line) {
                $commands[] = self::text(210, $lineY, 11, 'F2', $line, 0.125, 0.208, 0.192);
                $lineY -= 14;
            }
            $y = $lineY - 12;
        }

        if ($total !== '') {
            $commands[] = self::fill(0.906, 0.961, 0.941, 48, $y - 28, 499, 46);
            $commands[] = self::text(62, $y - 4, 9, 'F1', 'TOTAL AMOUNT', 0.376, 0.510, 0.478);
            $commands[] = self::text(62, $y - 22, 16, 'F2', $total, 0.090, 0.278, 0.247);
        }

        $commands[] = self::text(48, 54, 9, 'F1', 'Keep this PDF as your SpotTour booking record.', 0.443, 0.510, 0.498);

        return self::document(implode('', $commands));
    }

    public static function report(string $title, string $subtitle, array $headers, array $rows): string
    {
        $commands = [];
        $commands[] = self::fill(0.933, 0.957, 0.949, 0, 0, 595, 842);
        $commands[] = self::fill(0.090, 0.278, 0.247, 0, 780, 595, 62);
        $commands[] = self::text(36, 812, 18, 'F2', 'SpotTour', 1, 1, 1);
        $commands[] = self::text(36, 792, 11, 'F1', self::plain($title), 1, 1, 1);
        $commands[] = self::text(36, 750, 11, 'F2', self::plain($subtitle), 0.090, 0.278, 0.247);

        $y = 720;
        $widths = [190, 90, 70, 70, 110];
        $x = 36;
        foreach ($headers as $index => $header) {
            $commands[] = self::text($x, $y, 9, 'F2', self::plain($header), 0.376, 0.510, 0.478);
            $x += $widths[$index] ?? 80;
        }
        $y -= 8;
        $commands[] = self::fill(0.820, 0.890, 0.870, 36, $y, 523, 1);
        $y -= 18;

        foreach ($rows as $row) {
            if ($y < 48) {
                break;
            }
            $x = 36;
            foreach (array_values($row) as $index => $value) {
                $commands[] = self::text($x, $y, 10, 'F1', self::plain((string) $value), 0.125, 0.208, 0.192);
                $x += $widths[$index] ?? 80;
            }
            $y -= 18;
        }

        if ($rows === []) {
            $commands[] = self::text(36, $y, 11, 'F1', 'No bookings for this month.', 0.376, 0.510, 0.478);
        }

        $commands[] = self::text(36, 28, 9, 'F1', 'Tourism office visitor report. Booked means a reservation. Arrived means the owner checked the guest in.', 0.443, 0.510, 0.498);

        return self::document(implode('', $commands));
    }

    private static function plain(string $value): string
    {
        $value = trim(preg_replace('/\s+/', ' ', str_replace(["\r", "\n"], ' ', $value)) ?? '');
        $converted = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);

        return $converted === false
            ? (preg_replace('/[^\x20-\x7E]/', '', $value) ?? '')
            : $converted;
    }

    private static function wrap(string $text, int $width): array
    {
        return explode("\n", wordwrap($text === '' ? ' ' : $text, $width, "\n", true));
    }

    private static function escape(string $text): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }

    private static function fill(float $r, float $g, float $b, float $x, float $y, float $w, float $h): string
    {
        return sprintf("%.3F %.3F %.3F rg\n%.2F %.2F %.2F %.2F re\nf\n", $r, $g, $b, $x, $y, $w, $h);
    }

    private static function stroke(float $r, float $g, float $b, float $x, float $y, float $w, float $h): string
    {
        return sprintf("%.3F %.3F %.3F RG\n1 w\n%.2F %.2F %.2F %.2F re\nS\n", $r, $g, $b, $x, $y, $w, $h);
    }

    private static function text(float $x, float $y, int $size, string $font, string $text, float $r, float $g, float $b): string
    {
        return sprintf(
            "BT\n/%s %d Tf\n%.3F %.3F %.3F rg\n1 0 0 1 %.2F %.2F Tm\n(%s) Tj\nET\n",
            $font,
            $size,
            $r,
            $g,
            $b,
            $x,
            $y,
            self::escape($text)
        );
    }

    private static function document(string $content): string
    {
        $objects = [
            "1 0 obj<< /Type /Catalog /Pages 2 0 R >>endobj\n",
            "2 0 obj<< /Type /Pages /Count 1 /Kids [3 0 R] >>endobj\n",
            "3 0 obj<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << /Font << /F1 5 0 R /F2 6 0 R >> >> >>endobj\n",
            "4 0 obj<< /Length ".strlen($content)." >>stream\n".$content."endstream\nendobj\n",
            "5 0 obj<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>endobj\n",
            "6 0 obj<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>endobj\n",
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $object) {
            $offsets[] = strlen($pdf);
            $pdf .= $object;
        }

        $xref = strlen($pdf);
        $count = count($objects) + 1;
        $pdf .= "xref\n0 {$count}\n0000000000 65535 f \n";

        for ($index = 1; $index < $count; $index++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$index]);
        }

        $pdf .= "trailer<< /Size {$count} /Root 1 0 R >>\nstartxref\n{$xref}\n%%EOF";

        return $pdf;
    }
}
