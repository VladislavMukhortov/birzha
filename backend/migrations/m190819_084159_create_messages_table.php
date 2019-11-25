<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%messages}}`.
 */
class m190819_084159_create_messages_table extends Migration
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

        $this->createTable('{{%messages}}', [
            'id' => $this->primaryKey()->unsigned(),
            'offer_id' => $this->integer()->unsigned()->notNull(),      // ID оффера
            'sender_id' => $this->integer()->unsigned()->notNull(),     // ID пользователя - отправитель
            'receiver_id' => $this->integer()->unsigned()->notNull(),   // ID пользователя - получатель
            'type' => $this->tinyInteger()->notNull(),                  // тип контанта сообщения
            'text' => $this->string()->notNull(),                       // текст сообщения
            'translation' => $this->string()->defaultValue(null),       // перевод текста сообщения
            'is_new' => $this->boolean()->defaultValue(true),           // прочитано ли сообщение (false - прочитано, true - новое)
            'del_by_sender' => $this->boolean()->defaultValue(false),   // удалено отправителем
            'del_by_receiver' => $this->boolean()->defaultValue(false), // удалено получателем
            'notice' => $this->boolean()->defaultValue(false),          // уведомление (false - не отправленно, true - отправленно)
            'created_at' => 'timestamp DEFAULT NOW()',                  // дата создания
        ], $tableOptions);

        $table_name = $this->db->getSchema()->getRawTableName('{{%messages}}');

        // add foreign key for table `offers`
        $this->addForeignKey(
            "{$table_name}_offer_id_fk",
            "{$table_name}",
            'offer_id',
            '{{%offers}}',
            'id',
            'NO ACTION',
            'CASCADE'
        );

        // add foreign key for table `users`
        $this->addForeignKey(
            "{$table_name}_sender_id_fk",
            "{$table_name}",
            'sender_id',
            '{{%users}}',
            'id',
            'NO ACTION',
            'CASCADE'
        );

        // add foreign key for table `users`
        $this->addForeignKey(
            "{$table_name}_receiver_id_fk",
            "{$table_name}",
            'receiver_id',
            '{{%users}}',
            'id',
            'NO ACTION',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%messages}}');
    }
}
