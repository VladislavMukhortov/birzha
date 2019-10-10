<?php

return [
    'name' => 'Grain Market',
    'basePath' => dirname(__DIR__),
    'sourceLanguage' => 'en-US', // ru-RU
    'language' => 'en-US',
    'timeZone' => 'UTC',
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@tests' => '@app/tests',
    ],
    'components' => [
        'authManager' => [
            'class' => yii\rbac\PhpManager::class,
        ],
        'cache' => [
            'class' => yii\caching\FileCache::class,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                    'logVars' => ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION'],
                ],
            ],
        ],
        'mailer' => [
            'class' => yii\swiftmailer\Mailer::class,
            'htmlLayout' => 'layouts/html',
            'textLayout' => 'layouts/text',
            'messageConfig' => [
                'charset' => 'UTF-8',
                'from' => [
                    getenv('MAILER_USERNAME') => 'Metressa - онлайн знакомства'
                ]
            ],
            // dev - true | prod - false
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => getenv('MAILER_HOST'),
                'username' => getenv('MAILER_USERNAME'),
                'password' => getenv('MAILER_PASSWORD'),
                'port' => getenv('MAILER_PORT'),
                'encryption' => getenv('MAILER_ENCRYPTION')
            ]
        ],
        'db' => [
            'class' => yii\db\Connection::class,
            'dsn' => getenv('DB_DSN'),
            'username' => getenv('DB_USER'),
            'password' => getenv('DB_PASS'),
            'charset' => 'utf8',
            'enableSchemaCache' => true
            // 'schemaCacheDuration' => 60,
            // 'schemaCache' => 'cache'
        ],
        'formatter' => [
            'timeZone' => 'UTC',
            'dateFormat' => 'dd.MM.yyyy',
            'datetimeFormat' => 'HH:mm dd.MM.yyyy',
            'currencyCode' => 'USD',
            'decimalSeparator' => '.',
            'thousandSeparator' => ' ',
        ],
    ],
    'params' => require __DIR__ . '/params.php',
];
