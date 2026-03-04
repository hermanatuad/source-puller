<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "entity".
 *
 * @property int $id
 * @property string $entity_id
 * @property string $status
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
            [['id', 'entity_id', 'status', 'table_target'], 'required'],
            [['id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['entity_id', 'is_alive', 'status'], 'string', 'max' => 20],
            [['table_target'], 'string', 'max' => 255],
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
            'table_target' => 'Table Target',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getAffiliations()
    {
        return $this->hasMany(EntityAffiliation::class, ['entity_id' => 'entity_id']);
    }

    public function getSystems()
    {
        return $this->hasMany(EntitySystem::class, ['entity_id' => 'entity_id']);
    }

}
