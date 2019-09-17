<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\grid\GridView;

$this->title = 'Доска объявлений';
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
<style type="text/css">
.table > thead a {
    color: #ffc107;
}
</style>
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-8--- m-auto---">
                <h2><?= $this->title ?></h2>

                <hr>

                <?php echo GridView::widget([
                    'dataProvider' => $data_provider,
                    'filterModel' => $search_model,
                    'tableOptions' => [
                        'class' => 'table table-striped table-bordered table-hover table-sm'
                    ],
                    'headerRowOptions' => [
                        'class' => 'thead-dark'
                    ],
                    'layout' => '{summary}<div class="table-responsive">{items}</div>{pager}',
                    'columns' => [
                        [
                            'label' => 'Заголовок',
                            'attribute' => 'title',
                            'format' => 'raw',
                            'value' => function($data) {
                                return Html::a(h($data->title), ['declarations/index', 'link' => $data->link], ['class' => 'text-nowrap']);
                            }
                        ], [
                            'label' => 'Контрагент',
                            'attribute' => 'user_id',
                            'format' => 'raw',
                            'value' => function($data) {
                                $image = '';
                                // if ($data->users[0]->image) {
                                //     $image = Html::img(h($data->users[0]->image), ['class' => 'rounded mr-1', 'width' => '18px', 'height' => '18px']);
                                // }
                                $link = $image . Html::a(h($data->users[0]->name), ['traider/index', 'link' => $data->users[0]->link], ['class' => 'align-middle']);
                                return Html::tag('div', $link, ['class' => 'text-nowrap']);
                            }
                        ], [
                            'label' => 'Тип предложения',
                            'attribute' => 'deal',
                            'format' => 'raw',
                            'value' => function($data) {
                                return Html::tag('span', Yii::t('app', 'lot.deal.' . $data->deal), ['class' => 'text-info']);
                            },
                            'filter' => [
                                'buy' => Yii::t('app', 'lot.deal.buy'),
                                'sell' => Yii::t('app', 'lot.deal.sell')
                            ],
                            'filterInputOptions' => [
                                'prompt' => 'Не фильтровать',
                                'class' => 'form-control input-sm'
                            ]
                        ], [
                            'label' => 'Культура',
                            'attribute' => 'crops_id',
                            'format' => 'raw',
                            'value' => function($data) {
                                return Html::tag('span', Yii::t('app', 'crops.' . $data->crops[0]->name), ['class' => 'text-info']);
                            },
                            'filter' => [
                                '1' => Yii::t('app', 'crops.wheat'),
                                '2' => Yii::t('app', 'crops.barley'),
                                '3' => Yii::t('app', 'crops.corn')
                            ],
                            'filterInputOptions' => [
                                'prompt' => 'Не фильтровать',
                                'class' => 'form-control input-sm'
                            ]
                        ], [
                            'label' => 'Страна',
                            'attribute' => 'country_code',
                            'format' => 'text',
                            'filter' => [
                                'IR' => 'IR',
                                'TR' => 'TR',
                                'GE' => 'GE',
                                'AM' => 'AM',
                                'AZ' => 'AZ',
                                'RU' => 'RU',
                                'KZ' => 'KZ',
                                'UA' => 'UA'
                            ],
                            'filterInputOptions' => [
                                'prompt' => 'Не фильтровать',
                                'class' => 'form-control input-sm'
                            ]
                        ], [
                            'label' => 'Цена',
                            'attribute' => 'currency',
                            'format' => 'raw',
                            'value' => function($data) {
                                return Html::tag('span', formatter()->asCurrency($data->price, $data->currency), ['class' => 'text-nowrap']);
                            },
                            'filter' => [
                                'USD' => 'USD',
                                'EUR' => 'EUR',
                                'RUB' => 'RUB'
                            ],
                            'filterInputOptions' => [
                                'prompt' => 'Не фильтровать',
                                'class' => 'form-control input-sm'
                            ]
                        ], [
                            'label' => 'Объем (т.)',
                            'attribute' => 'quantity',
                            'format' => 'raw',
                            'value' => function($data) {
                                return Html::tag('span', formatter()->asDecimal($data->quantity, 0), ['class' => 'text-nowrap']);
                            }
                        ], [
                            'label' => 'Дата',
                            'attribute' => 'created_at',
                            'format' => 'raw',
                            'value' => function($data) {
                                return Html::tag('span', formatter()->asDate($data->created_at), ['class' => 'text-nowrap']);
                            }
                        ],
                    ],
                    'pager' => [
                        'options' => [
                            'class' => 'pagination pagination-sm',
                        ],
                        'linkContainerOptions' => [
                            'class' => 'page-item',
                        ],
                        'linkOptions' => [
                            'class' => 'page-link',
                        ],
                        'disabledListItemSubTagOptions' => [
                            'class' => 'page-link',
                        ],
                        'firstPageLabel' => '<<',
                        'lastPageLabel' => '>>',
                        'nextPageLabel' => '>',
                        'prevPageLabel' => '<',
                        'maxButtonCount' => 7,
                        'hideOnSinglePage' => false,
                    ]
                ]) ?>
            </div>
        </div>
    </div>
</section>
