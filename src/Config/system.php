<?php

return [
    [
        'key'    => 'sales.payment_methods.papara',
        'info'   => 'admin::app.configuration.index.sales.payment-methods.info',
        'name'   => 'papara::app.papara.name',
        'sort'   => 0,
        'fields' => [
            [
                'name'          => 'title',
                'title'         => 'admin::app.configuration.index.sales.payment-methods.title',
                'type'          => 'text',
                'depend'        => 'active:1',
                'validation'    => 'required_if:active,1',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'description',
                'title'         => 'admin::app.configuration.index.sales.payment-methods.description',
                'type'          => 'textarea',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'image',
                'title'         => 'admin::app.configuration.index.sales.payment-methods.image',
                'info'          => 'admin::app.configuration.index.sales.payment-methods.logo-information',
                'type'          => 'file',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'active',
                'title'         => 'admin::app.configuration.index.sales.payment-methods.status',
                'type'          => 'boolean',
                'channel_based' => false,
                'locale_based'  => true,
            ],
        ],
    ],
];
