<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%affiliation}}`.
 */
class mf260301_014408_create_afiliation_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%affiliation}}', [
            'id' => $this->primaryKey(),
            'affiliation_code' => $this->string()->notNull(),
            'affiliation_name' => $this->string()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // $this->dropTable('{{%affiliation}}');
        // $this->dropTable('{{%hospital}}');
    }
}
