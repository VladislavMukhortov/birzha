<?php

$config = [
    'id' => 'gm-backend-console',
    'controllerNamespace' => 'app\commands'
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => yii\gii\Module::class,
    ];
}

return yii\helpers\ArrayHelper::merge(require __DIR__ . '/common.php', $config);
