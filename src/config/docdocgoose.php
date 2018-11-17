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
    ]
];