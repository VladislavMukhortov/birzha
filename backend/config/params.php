<?php

return [
    // 'adminEmail' => 'admin@example.com',
    // 'senderEmail' => 'noreply@example.com',
    // 'senderName' => 'Example.com mailer',

    'mailer.adminEmail' => 'administrator@metressa.com',    //
    'mailer.supportEmail' => 'support@metressa.com',        //
    'mailer.noreplyEmail' => 'noreply@metressa.com',        //

    'cors.origin' => ['http://localhost:3000', 'http://ncc.local', 'http://127.0.0.1:80', 'http://dev.russiangrainmarket.ru'],

    'user.passwordResetTokenExpire' => 3600,                // 60*60 - 1 час
    'user.rememberLoginDuration' => 2592000,                // 60*60*24*30 - 30 дней // время жизни куки логина
    'user.rememberMeDuration' => 604800,                    // 60*60*24*7 - 7 дней // время жизни куки для автовхода


    'user.pathFullImage' => '/uploads/full/',
    'user.pathCropImage' => '/uploads/crop/',


    'db.commonDatetime' => 'php:Y-m-d H:i:s',               // шаблон времени для записи в БД

    'frontendURL' => 'http://dev.russiangrainmarket.ru/',
];
