<?php

use yii\helpers\Url;
use yii\helpers\Html;

$this->title = 'Главная';
$this->registerMetaTag(['name' => 'description', 'content' => '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => '']);
$this->registerMetaTag(['name' => 'og:title', 'content' => '']);
$this->registerMetaTag(['name' => 'og:type', 'content' => 'website']);
$this->registerMetaTag(['name' => 'og:image', 'content' => '']);
$this->registerMetaTag(['name' => 'og:url', 'content' => Url::to([request()->resolve()[0], 'language' => app()->language], true)]);
$this->registerMetaTag(['name' => 'og:locale', 'content' => app()->language]);
$this->registerMetaTag(['name' => 'og:description', 'content' => '']);
$this->registerMetaTag(['name' => 'og:site_name', 'content' => app()->name]);
$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::to([request()->resolve()[0], 'language' => app()->language], true)]);
$this->registerLinkTag(['rel' => 'image_src', 'href' => '']);

?>
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1>Hello this grain market web site</h1>

                <p>
                    <?php echo Html::a('Go to board', ['board/index']) ?>
                </p>
            </div>
        </div>
    </div>
</section>
