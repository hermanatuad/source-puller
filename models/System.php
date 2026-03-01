<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "system".
 *
 * @property string $id
 * @property string $system_code
 * @property string $system_name
 * @property string $system_type
 * @property string $database_name
 * @property string|null $hostname
 * @property string|null $username
 * @property string|null $password
 * @property string|null $port
 * @property string|null $path
 * @property string|null $description
 */
class System extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'system';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hostname', 'username', 'password', 'port', 'path', 'description'], 'default', 'value' => null],
            [['id', 'system_code', 'system_name', 'system_type', 'database_name'], 'required'],
            [['id'], 'string', 'max' => 36],
            [['system_code'], 'string', 'max' => 20],
            [['system_name', 'system_type', 'database_name', 'hostname', 'username', 'password', 'port', 'path', 'description'], 'string', 'max' => 255],
            [['system_code'], 'unique'],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'system_code' => 'System Code',
            'system_name' => 'System Name',
            'system_type' => 'System Type',
            'database_name' => 'Database Name',
            'hostname' => 'Hostname',
            'username' => 'Username',
            'password' => 'Password',
            'port' => 'Port',
            'path' => 'Path',
            'description' => 'Description',
        ];
    }

}
