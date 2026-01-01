<?php

use yii\db\Migration;

/**
 * Seeds initial users with roles
 */
class m260101_000003_seed_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Insert creator user
        $this->insert('{{%users}}', [
            'username' => 'creator',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash('creator123'),
            'email' => 'creator@example.com',
            'name' => 'Creator User',
            'status' => 10,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        // Insert admin user
        $this->insert('{{%users}}', [
            'username' => 'admin',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash('admin123'),
            'email' => 'admin@example.com',
            'name' => 'Admin User',
            'status' => 10,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        // Insert regular user
        $this->insert('{{%users}}', [
            'username' => 'user',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash('user123'),
            'email' => 'user@example.com',
            'name' => 'Regular User',
            'status' => 10,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%users}}', ['username' => 'creator']);
        $this->delete('{{%users}}', ['username' => 'admin']);
        $this->delete('{{%users}}', ['username' => 'user']);
    }
}
