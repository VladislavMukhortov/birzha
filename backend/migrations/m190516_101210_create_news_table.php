<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%news}}`.
 * Таблица для новостей
 */
class m190516_101210_create_news_table extends Migration
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

        $this->createTable('{{%news}}', [
            'id' => $this->primaryKey()->unsigned(),
            'url' => $this->string(255)->notNull()->unique(),               // url
            'title' => $this->string(255)->notNull()->defaultValue(null),   // заголовок
            'description' => $this->string(255)->defaultValue(null),        // описание
            'keywords' => $this->string(255)->defaultValue(null),           // ключевые слова
            'text' => $this->text()->defaultValue(null),                    // текст
            'views' => $this->integer()->unsigned()->defaultValue(0),       // кол-во просмотров
            'status' => $this->boolean()->defaultValue(true),               // статус статьи
            'created_at' => 'timestamp DEFAULT NOW()',                      // дата создания
            'updated_at' => 'timestamp ON UPDATE NOW()'                     // дата изменения
        ], $tableOptions);

        $table_name = $this->db->getSchema()->getRawTableName('{{%news}}');

        // creates index for column `url`
        $this->createIndex(
            "{$table_name}_url_idx",   // name
            "{$table_name}",           // table
            'url',                      // column
            true                        // unique
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%news}}');
    }
}
