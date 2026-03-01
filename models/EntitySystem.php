<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "entity_system".
 *
 * @property int $id
 * @property string $entity_id
 * @property string $entity_reference
 * @property string $system_code
 * @property string $created_at_data
 * @property string $updated_at_data
 * @property string $created_at
 * @property string $updated_at
 */
class EntitySystem extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'entity_system';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'entity_id', 'entity_reference', 'system_code', 'created_at_data'], 'required'],
            [['id'], 'integer'],
            [['created_at_data', 'updated_at_data', 'created_at', 'updated_at'], 'safe'],
            [['entity_id', 'system_code'], 'string', 'max' => 20],
            [['entity_reference'], 'string', 'max' => 255],
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
            'entity_reference' => 'Entity Reference',
            'system_code' => 'System Code',
            'created_at_data' => 'Created At Data',
            'updated_at_data' => 'Updated At Data',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

}
