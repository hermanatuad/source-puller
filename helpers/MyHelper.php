<?php

namespace app\helpers;

use Yii;

/**
 * MyHelper contains simple utility functions
 */
class MyHelper
{
    /**
     * Format number to Indonesian Rupiah currency format
     * 
     * @param float|int $amount The amount to format
     * @param bool $showSymbol Whether to show Rp symbol (default: true)
     * @param int $decimals Number of decimal places (default: 0)
     * @return string Formatted currency string
     */
    public static function idr($amount, $showSymbol = true, $decimals = 0)
    {
        $formatted = number_format($amount, $decimals, ',', '.');
        return $showSymbol ? 'Rp ' . $formatted : $formatted;
    }

    public static function genuuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * Shorten long text with ellipsis
     * 
     * @param string $text The text to shorten
     * @param int $length Maximum length (default: 100)
     * @param string $suffix Suffix to append (default: '...')
     * @return string Shortened text
     */
    public static function truncate($text, $length = 100, $suffix = '...')
    {
        if (mb_strlen($text) <= $length) {
            return $text;
        }
        return mb_substr($text, 0, $length) . $suffix;
    }

    /**
     * Format date to Indonesian format
     * 
     * @param string|int $date Date string or timestamp
     * @param string $format Format pattern (default: 'd F Y')
     * @return string Formatted date
     */
    public static function dateIndo($date, $format = 'd F Y')
    {
        $months = [
            'January' => 'Januari',
            'February' => 'Februari',
            'March' => 'Maret',
            'April' => 'April',
            'May' => 'Mei',
            'June' => 'Juni',
            'July' => 'Juli',
            'August' => 'Agustus',
            'September' => 'September',
            'October' => 'Oktober',
            'November' => 'November',
            'December' => 'Desember'
        ];

        $timestamp = is_numeric($date) ? $date : strtotime($date);
        $formatted = date($format, $timestamp);

        return str_replace(array_keys($months), array_values($months), $formatted);
    }

    /**
     * Convert Gregorian date to Hijri (Islamic) date.
     *
     * @param string|int|null $date Date string or timestamp. If null uses current date.
     * @param bool $asString If true returns formatted string like "1 Ramadhan 1445"; if false returns array.
     * @return array|string
     */
    public static function toHijri($date = null, $asString = true)
    {
        if ($date === null) {
            $ts = time();
        } elseif (is_numeric($date)) {
            $ts = (int) $date;
        } else {
            $ts = strtotime($date);
            if ($ts === false) {
                return $asString ? '' : [];
            }
        }

        $day = (int) gmdate('j', $ts);
        $month = (int) gmdate('n', $ts);
        $year = (int) gmdate('Y', $ts);

        $a = (int) floor((14 - $month) / 12);
        $y = $year + 4800 - $a;
        $m = $month + 12 * $a - 3;
        $jd = $day + (int) floor((153 * $m + 2) / 5) + 365 * $y + (int) floor($y / 4) - (int) floor($y / 100) + (int) floor($y / 400) - 32045;

        $l = $jd - 1948440 + 10632;
        $n = (int) floor(($l - 1) / 10631);
        $l = $l - 10631 * $n + 354;
        $j = (int) ((int) floor((10985 - $l) / 5316) * (int) floor((50 * $l) / 17719) + (int) floor($l / 5670) * (int) floor((43 * $l) / 15238));
        $l = $l - (int) floor((30 - $j) / 15) * (int) floor((17719 * $j) / 50) - (int) floor($j / 16) * (int) floor((15238 * $j) / 43) + 29;
        $mH = (int) floor((24 * $l) / 709);
        $dH = (int) ($l - (int) floor((709 * $mH) / 24));
        $yH = (int) (30 * $n + $j - 30);

        $months = [
            'Muharram', 'Safar', 'Rabiul Awwal', 'Rabiul Akhir',
            'Jumadil Awal', 'Jumadil Akhir', 'Rajab', "Sya'ban",
            'Ramadhan', 'Syawal', "Dzulqa'dah", "Dzulhijjah"
        ];

        $monthName = isset($months[$mH - 1]) ? $months[$mH - 1] : '';

        $result = [
            'year' => $yH,
            'month' => $mH,
            'day' => $dH,
            'monthName' => $monthName,
        ];

        if ($asString) {
            return $dH . ' ' . $monthName . ' ' . $yH;
        }

        return $result;
    }

    /**
     * Generate random string
     * 
     * @param int $length Length of random string (default: 10)
     * @return string Random string
     */
    public static function randomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Get file size in human readable format
     * 
     * @param int $bytes File size in bytes
     * @param int $precision Decimal precision (default: 2)
     * @return string Formatted file size
     */
    public static function fileSize($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Sanitize string for URL slug
     * 
     * @param string $string The string to convert
     * @return string URL-friendly slug
     */
    public static function slug($string)
    {
        $string = strtolower(trim($string));
        $string = preg_replace('/[^a-z0-9-]/', '-', $string);
        $string = preg_replace('/-+/', '-', $string);
        return trim($string, '-');
    }

    /**
     * Check if current user has permission
     * 
     * @param string|array $permission Permission name or array of permissions
     * @return bool
     */
    public static function can($permission)
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        
        if (is_array($permission)) {
            foreach ($permission as $perm) {
                if (Yii::$app->user->can($perm)) {
                    return true;
                }
            }
            return false;
        }
        
        return Yii::$app->user->can($permission);
    }
}
