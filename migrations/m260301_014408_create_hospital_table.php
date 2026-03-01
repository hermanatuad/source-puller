<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%hospital}}`.
 */
class m260301_014408_create_hospital_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%hospital}}', [
            'id' => $this->primaryKey(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%hospital}}');
    }
}
