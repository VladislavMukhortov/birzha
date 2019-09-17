<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Spaceless;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php // Spaceless::begin(); ?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?php echo app()->language ?>" dir="ltr">
<head>
    <meta charset="<?php echo app()->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?php echo h($this->title . ' | ' . app()->name) ?></title>
    <link rel="alternate" hreflang="<?php echo app()->language ?>" href="<?php echo Url::canonical() ?>" />
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="page">

    <header class="header">
        <?php echo $this->render('navbar') ?>
    </header>

    <div class="wrap">

        <main>
            <?php echo $content ?>
        </main>

        <footer class="footer bg-warning text-dark">
            <div class="container-fluid p-3 p-md-5">

                <p class="m-0">Запрещено копировать материалы сайта <?php echo Html::a(h($_SERVER['SERVER_NAME']), ['site/index']) ?> без прямой ссылки на источник</p>
                <p class="m-0">&copy;&nbsp;<?php echo date('Y') ?>&nbsp;<?php echo app()->name ?></p>
            </div>
        </footer>

    </div>

</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
<?php // Spaceless::end(); ?>
