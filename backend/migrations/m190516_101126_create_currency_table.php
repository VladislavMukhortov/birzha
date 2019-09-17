<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%currency}}`.
 * Таблица для валют
 */
class m190516_101126_create_currency_table extends Migration
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

        $this->createTable('{{%currency}}', [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string(255)->notNull()->unique(),      // название валюты
            'iso_code' => $this->char(3)->notNull()->unique(),      // цифровой код
            'iso_code_3' => $this->char(3)->notNull()->unique(),    // 3-ех символьный код (используется для пользователей)
            'status' => $this->boolean()->defaultValue(true),       // статус (активно | удалено)
            'sort' => $this->smallInteger()->defaultValue(1)        // порядок сортировки
        ], $tableOptions);

        $table_name = $this->db->getSchema()->getRawTableName('{{%currency}}');

        // creates index for column `iso_code_3`
        $this->createIndex(
            "{$table_name}_iso_code_3_idx", // name
            "{$table_name}",                // table
            'iso_code_3',                   // column
            true                            // unique
        );

        // add data
        // https://alpari.com/ru/beginner/articles/currency-codes/
        $this->batchInsert('{{%currency}}',
            ['id', 'name',          'iso_code', 'iso_code_3', 'status', 'sort'], [
            [1,    'US Dollar',     '840',      'USD',        true,     10],
            [2,    'Euro',          '978',      'EUR',        true,     20],
            [3,    'Russian Ruble', '643',      'RUB',        true,     30],
            [4,    'Hryvnia',       '980',      'UAH',        true,     40],
            [5,    'Tenge',         '398',      'KZT',        true,     50],
            [6,    'Yuan Renminbi', '156',      'CNY',        true,     60]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%currency}}');
    }
}
