<?php

return [
    [
        'key'    => 'sales.payment_methods.papara',
        'info'   => 'iyzico::app.papara.info',
        'name'   => 'iyzico::app.papara.name',
        'sort'   => 0,
        'fields' => [
            [
                'name'          => 'title',
                'title'         => 'papara::app.papara.system.title',
                'type'          => 'text',
                'depend'        => 'active:1',
                'validation'    => 'required_if:active,1',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'description',
                'title'         => 'papara::app.papara.system.description',
                'type'          => 'textarea',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'image',
                'title'         => 'papara::app.papara.system.image',
                'info'          => 'admin::app.configuration.index.sales.payment-methods.logo-information',
                'type'          => 'file',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'active',
                'title'         => 'papara::app.papara.system.status',
                'type'          => 'boolean',
                'channel_based' => false,
                'locale_based'  => true,
            ],
        ],
    ],
];
