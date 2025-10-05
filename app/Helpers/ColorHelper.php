<?php

namespace App\Helpers;

class ColorHelper
{
    public static function generateRandomBrightColor()
    {
        // Generate three random numbers for RGB components
        $rgb = [rand(0, 255), rand(0, 255), rand(0, 255)];

        // Ensure at least one of the RGB components is high (greater than 200)
        if (max($rgb) < 200) {
            $rgb[array_rand($rgb)] = rand(200, 255);
        }

        // Convert each component to a hexadecimal string and return the concatenated result
        return '#' . sprintf('%02x%02x%02x', $rgb[0], $rgb[1], $rgb[2]);
    }

    public static function hexToRgb($hex)
    {
        $hex = ltrim($hex, '#');

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }

    public static function rgbToHex($r, $g, $b)
    {
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    public static function adjustHexLightness($colorInRgb, $percent)
    {
        $adjust = function ($component) use ($percent) {
            return round($component + (255 - $component) * $percent);
        };

        return array_map($adjust, $colorInRgb);
    }
}
