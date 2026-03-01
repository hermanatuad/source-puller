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
            [['id', 'entity_id', 'entity_reference', 'system_code'], 'required'],
            [['id'], 'integer'],
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
        ];
    }

}
