<?php

namespace App\Helpers;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;

class PremiumHelper
{
    public static function generateQrCode(string $btcAddress)
    {
        $writer = new PngWriter();
        $qrCode = new QrCode("bitcoin:$btcAddress");
        $result = $writer->write($qrCode);

        $filename = $btcAddress . '.png';
        $path = "qr-codes/{$filename}";

        // Store it using the 'public' disk
        Storage::disk('public')->put($path, $result->getString());
    }
}
