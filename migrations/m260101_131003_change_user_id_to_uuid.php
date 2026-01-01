<?php

use yii\db\Migration;

class m260101_131003_change_user_id_to_uuid extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Disable foreign key checks
        $this->execute("SET FOREIGN_KEY_CHECKS = 0");
        
        // Drop mapping table if exists
        $this->execute("DROP TABLE IF EXISTS {{%user_id_mapping}}");
        
        // Backup existing data
        $users = $this->db->createCommand("SELECT * FROM {{%users}}")->queryAll();
        
        // Create temporary mapping table
        $this->createTable('{{%user_id_mapping}}', [
            'old_id' => $this->integer()->notNull(),
            'new_id' => $this->string(36)->notNull(),
            'PRIMARY KEY(old_id)',
        ]);
        
        // Generate UUIDs and store mapping
        foreach ($users as $user) {
            $oldId = $user['id'];
            $newId = $this->generateUUID();
            $this->insert('{{%user_id_mapping}}', ['old_id' => $oldId, 'new_id' => $newId]);
        }
        
        // Modify users.id to VARCHAR(36) for UUID
        $this->execute("ALTER TABLE {{%users}} MODIFY COLUMN id VARCHAR(36) NOT NULL");
        
        // Modify auth_assignment.user_id to VARCHAR(36)
        $this->execute("ALTER TABLE {{%auth_assignment}} MODIFY COLUMN user_id VARCHAR(36) NOT NULL");
        
        // Update users with new UUIDs using raw SQL
        $mappings = $this->db->createCommand("SELECT * FROM {{%user_id_mapping}}")->queryAll();
        foreach ($mappings as $mapping) {
            $this->execute("UPDATE {{%users}} SET id = :new_id WHERE id = :old_id", [
                ':new_id' => $mapping['new_id'],
                ':old_id' => (string)$mapping['old_id']
            ]);
            $this->execute("UPDATE {{%auth_assignment}} SET user_id = :new_id WHERE user_id = :old_id", [
                ':new_id' => $mapping['new_id'],
                ':old_id' => (string)$mapping['old_id']
            ]);
        }
        
        // Drop mapping table
        $this->dropTable('{{%user_id_mapping}}');
        
        // Re-enable foreign key checks
        $this->execute("SET FOREIGN_KEY_CHECKS = 1");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m260101_131003_change_user_id_to_uuid cannot be reverted.\n";
        return false;
    }
    
    /**
     * Generate UUID v4
     * @return string
     */
    private function generateUUID()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}
