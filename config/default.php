<?php
declare(strict_types=1);

return [
    'base_path' => '',

    'namespace' => '',

    'stub' => [
        'use_case' => realpath(__DIR__.'/../stub/DummyUseCase'),
        'request' => realpath(__DIR__.'/../stub/DummyRequest'),
        'response' => realpath(__DIR__.'/../stub/DummyResponse'),
        'interactor' => realpath(__DIR__.'/../stub/DummyInteractor'),
    ],

    'class' => [
        'use_case' => '__USE_CASE__Interface',
        'request' => 'Request',
        'response' => 'Response',
        'interactor' => '__USE_CASE__',
    ],
];
