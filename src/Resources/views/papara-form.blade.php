<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ core()->getCurrentLocale()->direction }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url()->to('/') }}">
    <meta name="currency-code" content="{{ core()->getCurrentCurrencyCode() }}">
    <meta http-equiv="content-language" content="{{ app()->getLocale() }}">

    <title>{{ __('papara::app.resources.title') }}</title>

    <link
        rel="icon"
        sizes="16x16"
        href="{{ core()->getCurrentChannel()->favicon_url ?? bagisto_asset('images/favicon.ico', 'shop') }}"
    />
</head>
<body>
{!! $paymentForm !!}
<div id="papara-checkout-form" class="responsive">
    @csrf
</div>
</body>
</html>
