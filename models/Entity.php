<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "entity".
 *
 * @property int $id
 * @property string $entity_id
 * @property string $status
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
            [['id', 'entity_id', 'status'], 'required'],
            [['id'], 'integer'],
            [['entity_id', 'status'], 'string', 'max' => 20],
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
            'status' => 'Status',
        ];
    }

}
