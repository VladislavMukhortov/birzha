<?php

use yii\helpers\Url;
use yii\helpers\Html;

$this->title = 'Объявление #' . $declaration->id;
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
<section class="py-5 bg-light signin">
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-8 m-auto">

                <p>
                    <?php echo Html::a('Back to board', ['board/index']) ?>
                </p>

                <h2><?php echo h($this->title) ?></h2>

                <hr>

                <div class="bg-white p-3">
                    <h4><?php echo h($declaration->title) ?></h4>
                    <p><span class="text-muted">Deal: </span><?php echo Yii::t('app', 'lot.deal.' . $declaration->deal) ?></p>
                    <p><span class="text-muted">Crops: </span><?php echo Yii::t('app', 'crops.' . $crop->name) ?></p>
                    <p><span class="text-muted">Price: </span><?php echo formatter()->asCurrency($declaration->price, $declaration->currency) ?></p>
                    <p><span class="text-muted">Quantity: </span><?php echo h($declaration->quantity) ?></p>
                    <p><span class="text-muted">Country: </span><?php echo h($declaration->country_code) ?></p>
                    <p>
                        <span class="text-muted">Сounterparty: </span>
                        <?php echo Html::a(h($user->name), ['traider/index', 'link' => $user->link]) ?>
                    </p>
                    <p><span class="text-muted">Views: </span><?php echo h($declaration->views) ?></p>
                    <p><span class="text-muted">Created: </span><?php echo h($declaration->created_at) ?></p>
                </div>

            </div>
        </div>
    </div>
</section>

