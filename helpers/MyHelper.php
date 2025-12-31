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
