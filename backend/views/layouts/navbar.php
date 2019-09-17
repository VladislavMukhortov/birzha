<?php

use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;

NavBar::begin([
    'brandLabel' => app()->name,
    'brandUrl' => app()->homeUrl,
    'options' => [
        'id' => 'navbar',
        'class' => 'navbar navbar-expand-lg sticky-top navbar-light bg-warning'
    ],
    'innerContainerOptions' => [
        'class' => 'container'
    ],
    'containerOptions' => [
        'class' => 'flex-row-reverse'
    ]
]);

if (user()->isGuest) {
    $items = [
        ['label' => 'Main', 'url' => ['/site/index']],
        ['label' => 'Board', 'url' => ['/board/index']],
        ['label' => 'Signin', 'url' => ['/auth/signin/index'], 'linkOptions' => ['class' => 'btn btn-dark text-white mr-1']],
        ['label' => 'Signup', 'url' => ['/auth/signup/index'], 'linkOptions' => ['class' => 'btn btn-light text-dark']],
    ];
} else {
    $items = [
        ['label' => 'Main', 'url' => ['/site/index']],
        ['label' => 'Board', 'url' => ['/board/index']],
        ['label' => 'My profile', 'url' => ['traider/my']],
        ['label' => 'Logout', 'url' => ['/auth/logout/index'], 'linkOptions' => ['data-method' => 'post', 'class' => 'btn btn-danger text-white']]
    ];
}

echo Nav::widget([
    'options' => ['class' => 'navbar-nav'],
    'items' => $items
]);

NavBar::end();
