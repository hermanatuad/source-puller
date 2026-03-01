<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "entity_affiliation".
 *
 * @property int $id
 * @property string $entity_id
 * @property string $affiliation_code
 */
class EntityAffiliation extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'entity_affiliation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'entity_id', 'affiliation_code'], 'required'],
            [['id'], 'integer'],
            [['entity_id', 'affiliation_code'], 'string', 'max' => 20],
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
            'affiliation_code' => 'Affiliation Code',
        ];
    }

}
