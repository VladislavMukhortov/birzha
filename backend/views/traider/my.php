<?php

use yii\helpers\Url;
use yii\helpers\Html;

$this->title = 'My profile';
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

                <h2><?php echo h($this->title) ?></h2>

                <hr>

               <div class="bg-white p-3">
                    <?php
                        // if ($user->image) {
                        //     echo Html::img(h($user->image), ['class' => 'rounded mb-3']);
                        // }
                    ?>
                    <h4><?php echo h($user->name) ?></h4>
                    <p><span class="text-muted">Email: </span><?php echo h($user->email) ?></p>
                    <p><span class="text-muted">Phone: </span><?php echo h($user->phone) ?></p>
                    <p><span class="text-muted">Country: </span><?php echo h($user->country_code) ?></p>
                </div>

            </div>
        </div>
    </div>
</section>

