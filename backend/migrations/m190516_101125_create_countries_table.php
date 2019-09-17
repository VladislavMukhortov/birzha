<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%countries}}`.
 * Таблица для стран
 */
class m190516_101125_create_countries_table extends Migration
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

        $this->createTable('{{%countries}}', [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string(255)->notNull()->unique(),      // название страны
            'iso_code' => $this->char(3)->notNull()->unique(),      // цифровой код
            'iso_code_2' => $this->char(2)->notNull()->unique(),    // 2-ух символьный код (используется для пользователей)
            'iso_code_3' => $this->char(3)->notNull()->unique(),    // 3-ех символьный код
            'status' => $this->boolean()->defaultValue(true),       // статус (активно | удалено)
            'sort' => $this->smallInteger()->defaultValue(1)        // порядок сортировки
        ], $tableOptions);

        $table_name = $this->db->getSchema()->getRawTableName('{{%countries}}');

        // creates index for column `iso_code_2`
        $this->createIndex(
            "{$table_name}_iso_code_2_idx", // name
            "{$table_name}",                // table
            'iso_code_2',                   // column
            true                            // unique
        );

        // add data
        // https://mvf.klerk.ru/spr/spr63.htm
        $this->batchInsert('{{%countries}}',
            ['id', 'name',       'iso_code', 'iso_code_2', 'iso_code_3', 'status', 'sort'], [
            [1,    'iran',       '364',      'IR',         'IRN',        true,     10],
            [2,    'turkey',     '792',      'TR',         'TUR',        true,     20],
            [3,    'georgia',    '268',      'GE',         'GEO',        true,     30],
            [4,    'armenia',    '051',      'AM',         'ARM',        true,     40],
            [5,    'azerbaijan', '031',      'AZ',         'AZE',        true,     50],
            [6,    'russia',     '643',      'RU',         'RUS',        true,     60],
            [7,    'kazakhstan', '398',      'KZ',         'KAZ',        true,     70],
            [8,    'ukraine',    '804',      'UA',         'UKR',        true,     80]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%countries}}');
    }
}
