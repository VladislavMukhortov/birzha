<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%crops}}`.
 * Таблица для культур
 */
class m190516_101127_create_crops_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%crops}}', [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string(255)->notNull()->unique(),  // название культуры
            'status' => $this->boolean()->defaultValue(true),   // статус (активно | удалено)
            'sort' => $this->smallInteger()->defaultValue(1)    // порядок сортировки
        ], $tableOptions);

        // add data
        $this->batchInsert('{{%crops}}',
            ['id', 'name',   'status',   'sort'], [
            [1,    'wheat',     true,     5],   // пшеница
            [2,    'durum',     true,     10],  // пшеница твердая
            [3,    'barley',    true,     15],  // ячмень
            [4,    'corn',      true,     20],  // кукуруза
            [5,    'flax',      true,     25],  // лен
            [6,    'rape',      true,     30],  // рапс
            [7,    'peas',      true,     35],  // горох
            [8,    'soybeans',  true,     40],  // соевые бобы
            [9,    'sunflower', true,     45],  // подсолнечник
            [10,   'chickpeas', true,     50],  // нут
            [11,   'wild_flax', true,     55],  // рыжик
            [12,   'safflower', true,     60],  // сафлор
            [13,   'sorghum',   true,     65],  // сорго
            [14,   'millet',    true,     70],  // просо
            [15,   'coriander', true,     75],  // кориандр
            [16,   'mustard',   true,     80],  // горчица
            [17,   'lentil',    true,     85],  // чечевица
            [18,   'rye',       true,     90],  // рожь
            [19,   'oat',       true,     95],  // овес
            [20,   'buckwheat', true,     100], // гречиха
            [21,   'triticale', true,     105], // тритикале
            [22,   'rice',      true,     110], // рис
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%crops}}');
    }
}
