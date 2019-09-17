<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%basis}}`.
 * Таблица для базисов
 */
class m190705_110340_create_basis_table extends Migration
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

        $this->createTable('{{%basis}}', [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->char(3)->notNull()->unique(),      // название базиса
            'status' => $this->boolean()->defaultValue(true),   // статус (активно | удалено)
            'sort' => $this->smallInteger()->defaultValue(1)    // порядок сортировки
        ], $tableOptions);

        $table_name = $this->db->getSchema()->getRawTableName('{{%basis}}');

        // creates index for column `name`
        $this->createIndex(
            "{$table_name}_name_idx",   // name
            "{$table_name}",            // table
            'name',                     // column
            true                        // unique
        );

        // add data
        $this->batchInsert('{{%basis}}',
            ['id', 'name',  'status',   'sort'], [
            [1,    'EXW',   false,      10],
            [2,    'FCA',   false,      20],
            [3,    'CPT',   false,      30],
            [4,    'CIP',   false,      40],
            [5,    'DAT',   false,      50],
            [6,    'DAP',   false,      60],
            [7,    'DDP',   false,      70],
            [8,    'FAS',   false,      80],
            [9,    'FOB',   true,       90],
            [10,   'CFR',   false,      100],
            [11,   'CIF',   true,       110]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%basis}}');
    }
}
