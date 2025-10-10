<?php

namespace App\Helpers;

class Terbilang
{
    protected static $dasar = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan'];
    protected static $belasan = ['sepuluh', 'sebelas', 'dua belas', 'tiga belas', 'empat belas', 'lima belas', 'enam belas', 'tujuh belas', 'delapan belas', 'sembilan belas'];
    protected static $puluhan = ['', '', 'dua puluh', 'tiga puluh', 'empat puluh', 'lima puluh', 'enam puluh', 'tujuh puluh', 'delapan puluh', 'sembilan puluh'];
    protected static $satuan = ['', 'ribu', 'juta', 'miliar', 'triliun'];

    public static function make($angka)
    {
        if ($angka < 12) {
            return static::$dasar[$angka] ?? static::$belasan[$angka - 10];
        }
        if ($angka < 100) {
            return static::$puluhan[$angka / 10] . (($angka % 10 != 0) ? " " . static::make($angka % 10) : '');
        }
        if ($angka < 200) {
            return "seratus" . (($angka - 100 != 0) ? " " . static::make($angka - 100) : '');
        }
        if ($angka < 1000) {
            return static::$dasar[$angka / 100] . " ratus" . (($angka % 100 != 0) ? " " . static::make($angka % 100) : '');
        }
        if ($angka < 2000) {
            return "seribu" . (($angka - 1000 != 0) ? " " . static::make($angka - 1000) : '');
        }
        if ($angka < 1000000) {
            return static::make($angka / 1000) . " ribu" . (($angka % 1000 != 0) ? " " . static::make($angka % 1000) : '');
        }
        if ($angka < 1000000000) {
            return static::make($angka / 1000000) . " juta" . (($angka % 1000000 != 0) ? " " . static::make($angka % 1000000) : '');
        }
        return 'Angka terlalu besar';
    }
}
