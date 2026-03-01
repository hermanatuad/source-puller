<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%patients}}`.
 */
class m260301_013829_create_patients_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%patients}}', [
            'id' => $this->primaryKey(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%patients}}');
    }
}
