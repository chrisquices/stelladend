<?php

namespace App\Services;

use App\Models\Security;
use App\Models\Dividend;
use Carbon\Carbon;

class AdminService
{
    public function processStockAnalysisDividendData($securityId, $file)
    {
        $security = Security::findOrFail($securityId);

        $handle = fopen($file->getRealPath(), 'r');
        if (!$handle) {
            throw new \RuntimeException("Unable to open CSV file");
        }

        $header = null;

        while (($line = fgetcsv($handle, 1000, ',')) !== false) {
            if (!$header) {
                $header = array_map(function ($value) {
                    return trim(preg_replace('/^\xEF\xBB\xBF/', '', $value));
                }, $line);
                continue;
            }

            $data = array_combine($header, $line);
            if (!$data || empty($data['Pay Date']) || empty($data['Cash Amount'])) {
                continue;
            }

            try {
                $paymentDate = Carbon::parse($data['Pay Date']);
                $exDate = !empty($data['Ex-Dividend Date']) ? Carbon::parse($data['Ex-Dividend Date']) : null;
                $recordDate = !empty($data['Record Date']) ? Carbon::parse($data['Record Date']) : null;
                $declarationDate = (!empty($data['Declaration Date']) && strtolower($data['Declaration Date']) !== 'n/a')
                    ? Carbon::parse($data['Declaration Date']) : null;
            } catch (\Exception $e) {
                continue;
            }

            $amount = floatval($data['Cash Amount']);
            if ($amount <= 0) {
                continue;
            }

            Dividend::updateOrCreate(
                [
                    'security_id'  => $security->id,
                    'payment_date' => $paymentDate->format('Y-m-d'),
                ],
                [
                    'amount'   => $amount,
                    'ex_dividend_date'  => $exDate,
                    'record_date'       => $recordDate,
                    'declaration_date'  => $declarationDate,
                    'source'            => 'stockanalysis',
                ]
            );
        }

        fclose($handle);
    }
}
