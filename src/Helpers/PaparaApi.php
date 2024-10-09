<?php

namespace Webkul\Papara\Helpers;

use Papara\Options;

/**
 * Papara payment api and secret key
 */
class PaparaApi
{
    public static function options(): Options
    {
        $options = new Options();
        $options->setApiKey(env('PAPARA_API_KEY', 'null'));
        $options->setSecretKey(env('PAPARA_SECRET_KEY', 'null'));
        $options->setBaseUrl(env('PAPARA_BASE_URL', 'https://merchant-api.papara.com/v1/vpos/sale'));

        return $options;
    }
}
