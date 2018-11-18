<?php

return [
    'routes' => [
        'v1' => [
            'patterns' => [ 'api.v1.*' ],
            'rules' => [
                'headers' => [
                    'Authorization' => '<Your API Key>'
                ]
            ]
        ]
    ],
    'cache' => [
        'enabled' => true,
        'store' => 'file'
    ]
];