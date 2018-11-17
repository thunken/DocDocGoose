<?php

return [
    'routes' => [
        'patterns' => [ 'api.*' ],
    ],
    'rules' => [
        'headers' => [
            'Authorization' => '<Your API Key>'
        ]
    ]
];