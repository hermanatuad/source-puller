<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%users}}`.
 */
class m260101_000001_create_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(255)->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string(255)->notNull(),
            'password_reset_token' => $this->string(255)->unique(),
            'email' => $this->string(255)->notNull()->unique(),
            'name' => $this->string(255),
            'telegram_id' => $this->string(100),
            'phone_number' => $this->string(20),
            'whatsapp_number' => $this->string(20),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        // Create index for username
        $this->createIndex(
            'idx-users-username',
            '{{%users}}',
            'username'
        );

        // Create index for email
        $this->createIndex(
            'idx-users-email',
            '{{%users}}',
            'email'
        );

        // Create index for status
        $this->createIndex(
            'idx-users-status',
            '{{%users}}',
            'status'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%users}}');
    }
}
