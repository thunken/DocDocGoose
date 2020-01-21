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
    ],
    'strategies' => [
        'metadata' => [
            \Mpociot\ApiDoc\Extracting\Strategies\Metadata\GetFromDocBlocks::class,
        ],
        'urlParameters' => [
            \Mpociot\ApiDoc\Extracting\Strategies\UrlParameters\GetFromUrlParamTag::class,
        ],
        'queryParameters' => [
            \Thunken\DocDocGoose\Extracting\Strategies\GetFromExtendedQueryParamTag::class,
        ],
        'headers' => [
            \Mpociot\ApiDoc\Extracting\Strategies\RequestHeaders\GetFromRouteRules::class,
        ],
        'bodyParameters' => [
            \Mpociot\ApiDoc\Extracting\Strategies\BodyParameters\GetFromBodyParamTag::class,
        ],
        'responses' => [
            \Mpociot\ApiDoc\Extracting\Strategies\Responses\UseTransformerTags::class,
            \Mpociot\ApiDoc\Extracting\Strategies\Responses\UseResponseTag::class,
            \Mpociot\ApiDoc\Extracting\Strategies\Responses\UseResponseFileTag::class,
            \Mpociot\ApiDoc\Extracting\Strategies\Responses\UseApiResourceTags::class,
            \Mpociot\ApiDoc\Extracting\Strategies\Responses\ResponseCalls::class,
        ],
    ],
];