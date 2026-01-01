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
        // Insert admin user
        $this->insert('{{%users}}', [
            'username' => 'admin',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash('admin123'),
            'email' => 'admin@example.com',
            'status' => 10,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        // Insert demo user
        $this->insert('{{%users}}', [
            'username' => 'demo',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash('demo123'),
            'email' => 'demo@example.com',
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
        $this->delete('{{%users}}', ['username' => 'admin']);
        $this->delete('{{%users}}', ['username' => 'demo']);
    }
}
