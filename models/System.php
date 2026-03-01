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
 * @property string $hostname
 * @property string $password
 * @property string $port
 * @property string $path
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
            [['description'], 'default', 'value' => null],
            [['id', 'system_code', 'system_name', 'system_type', 'hostname', 'password', 'port', 'path'], 'required'],
            [['id'], 'string', 'max' => 36],
            [['system_code'], 'string', 'max' => 20],
            [['system_name', 'system_type', 'hostname', 'password', 'port', 'path', 'description'], 'string', 'max' => 255],
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
            'hostname' => 'Hostname',
            'password' => 'Password',
            'port' => 'Port',
            'path' => 'Path',
            'description' => 'Description',
        ];
    }

}
