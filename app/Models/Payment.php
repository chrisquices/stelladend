<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function btcAddress()
    {
        return $this->belongsTo(BtcAddress::class);
    }

    public function getQrCodeUrlAttribute() {
        return config('app.url') . '/storage/qr-codes/' . $this->btcAddress->address . '.png';
    }
}
