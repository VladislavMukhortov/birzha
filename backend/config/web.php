<?php

$config = [
    'id' => 'gm-backend',
    'defaultRoute' => 'site',
    'bootstrap' => [
        [
            'class' => yii\filters\ContentNegotiator::className(),
            'formats' => [
                'application/json' => yii\web\Response::FORMAT_JSON,
                'text/html' => yii\web\Response::FORMAT_HTML
            ],
            'languages' => ['en', 'ru'],
            // 'languages' => ['en'],
        ]
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => getenv('COOKIE_VALIDATION_KEY'),
        ],
        'user' => [
            'identityClass' => app\models\User::class,
            'enableAutoLogin' => false,
            'loginUrl' => ['auth/signin/index']
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'normalizer' => [
                'class' => yii\web\UrlNormalizer::class
            ],
            'rules' => [
                'OPTIONS api/auth/<controller>/<action>' => 'api/auth/<controller>/options',
                'OPTIONS api/basis/<controller>/<action>' => 'api/basis/<controller>/options',
                'OPTIONS api/company/<controller>/<action>' => 'api/company/<controller>/options',
                'OPTIONS api/crop/<controller>/<action>' => 'api/crop/<controller>/options',
                'OPTIONS api/currency/<controller>/<action>' => 'api/currency/<controller>/options',
                'OPTIONS api/lot/<controller>/<action>' => 'api/lot/<controller>/options',
                'OPTIONS api/messages/<controller>/<action>' => 'api/messages/<controller>/options',
                'OPTIONS api/offer/<controller>/<action>' => 'api/offer/<controller>/options',
                'OPTIONS api/user/<controller>/<action>' => 'api/user/<controller>/options',

                '' => 'site/index',
                'auth/<controller:(signin|signup|logout)>' => 'auth/<controller>/index',

                'board' => 'board/index',
                'traider/my' => 'traider/my',
                'declarations/<link:[a-zA-Z0-9_-]+>' => 'declarations/index',
                'traider/<link:[a-zA-Z0-9_-]+>' => 'traider/index'
            ],
        ],
        // подключаем файл перевода сообщений сайта
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => yii\i18n\PhpMessageSource::class,
                    'basePath' => '@app/language',
                    'fileMap' => [
                        'app' => 'app.php'
                    ]
                ]
            ]
        ],
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => yii\debug\Module::class,
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => yii\gii\Module::class,
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return yii\helpers\ArrayHelper::merge(require __DIR__ . '/common.php', $config);
