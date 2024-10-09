<?php

namespace Webkul\Papara\Payment;

use Illuminate\Support\Facades\Storage;
use Webkul\Payment\Payment\Payment;

class Papara extends Payment
{
    /**
     * Payment method code
     */
    protected string $code = 'papara';

    public function getRedirectUrl(): string
    {
        return route('papara.redirect');
    }

    /**
     * Returns payment method image
     */
    public function getImage(): string
    {
        $url = $this->getConfigData('image');

        return $url ? Storage::url($url) : bagisto_asset('images/money-transfer.png', 'shop');
    }
}
