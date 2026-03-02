<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "entity".
 *
 * @property int $id
 * @property string $entity_id
 * @property string $status
 * @property string $created_at_data
 * @property string $updated_at_data
 * @property string $created_at
 * @property string $updated_at
 */
class Entity extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'entity';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'entity_id', 'status', 'created_at_data'], 'required'],
            [['id'], 'integer'],
            [['created_at_data', 'updated_at_data', 'created_at', 'updated_at'], 'safe'],
            [['entity_id', 'is_alive', 'status'], 'string', 'max' => 20],
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
            'entity_id' => 'Entity ID',
            'is_alive' => 'Live Status',
            'status' => 'Status',
            'created_at_data' => 'Created At Data',
            'updated_at_data' => 'Updated At Data',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

}
