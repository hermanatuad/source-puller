<?php

use yii\db\Migration;

class m260101_122412_add_access_role_to_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%users}}', 'access_role', $this->string(50)->after('status'));
        
        // Create index for access_role
        $this->createIndex(
            'idx-users-access_role',
            '{{%users}}',
            'access_role'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop index
        $this->dropIndex('idx-users-access_role', '{{%users}}');
        
        // Drop column
        $this->dropColumn('{{%users}}', 'access_role');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260101_122412_add_access_role_to_users_table cannot be reverted.\n";

        return false;
    }
    */
}
