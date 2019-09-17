<?php

use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;

NavBar::begin([
    'brandLabel' => app()->name,
    'brandUrl' => app()->homeUrl,
    'options' => [
        'id' => 'navbar',
        'class' => 'navbar navbar-expand-lg sticky-top navbar-dark bg-dark'
    ],
    'innerContainerOptions' => [
        'class' => 'container'
    ],
    'containerOptions' => [
        'class' => 'flex-row-reverse'
    ]
]);

echo Nav::widget([
    'options' => ['class' => 'navbar-nav'],
    'items' => [
        ['label' => 'Dashboard', 'url' => ['/admin/dashboard/index']],
        ['label' => 'Api', 'url' => ['/admin/api/index']],
        [
            'label' => 'Logout',
            'url' => ['/auth/logout/index'],
            'linkOptions' => ['data-method' => 'post', 'class' => 'btn btn-danger text-light']
        ]
    ]
]);

NavBar::end();
