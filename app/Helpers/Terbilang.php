<?php

namespace App\Helpers;

class Terbilang
{
    public static function formatRp(float $value): string
    {
        return $value == 0 ? '-' : 'Rp ' . number_format($value, 0, ',', '.');
    }
    protected static array $angka = [
        '', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh',
        'Sebelas',
    ];

    public static function convert(int $number): string
    {
        if ($number < 12) {
            return self::$angka[$number];
        } elseif ($number < 20) {
            return self::convert($number - 10) . ' Belas';
        } elseif ($number < 100) {
            return self::convert(intdiv($number, 10)) . ' Puluh ' . self::convert($number % 10);
        } elseif ($number < 200) {
            return 'Seratus ' . self::convert($number - 100);
        } elseif ($number < 1000) {
            return self::convert(intdiv($number, 100)) . ' Ratus ' . self::convert($number % 100);
        } elseif ($number < 2000) {
            return 'Seribu ' . self::convert($number - 1000);
        } elseif ($number < 1000000) {
            return self::convert(intdiv($number, 1000)) . ' Ribu ' . self::convert($number % 1000);
        } elseif ($number < 1000000000) {
            return self::convert(intdiv($number, 1000000)) . ' Juta ' . self::convert($number % 1000000);
        }
        return (string) $number;
    }

    public static function rupiah(float $number): string
    {
        $result = trim(preg_replace('/\s+/', ' ', self::convert((int) $number)));
        return $result . ' Rupiah';
    }
}