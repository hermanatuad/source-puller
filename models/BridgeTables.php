<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bridge_tables".
 *
 * @property string $id
 * @property string $bridge_id
 * @property string $source_table_name
 * @property string $target_table_name
 *
 * @property Bridge $bridge
 */
class BridgeTables extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bridge_tables';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'bridge_id', 'source_table_name', 'target_table_name'], 'required'],
            [['id', 'bridge_id'], 'string', 'max' => 36],
            [['source_table_name', 'target_table_name'], 'string', 'max' => 255],
            [['id'], 'unique'],
            [['bridge_id'], 'exist', 'skipOnError' => true, 'targetClass' => Bridge::class, 'targetAttribute' => ['bridge_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bridge_id' => 'Bridge ID',
            'source_table_name' => 'Source Table Name',
            'target_table_name' => 'Target Table Name',
        ];
    }

    /**
     * Gets query for [[Bridge]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBridge()
    {
        return $this->hasOne(Bridge::class, ['id' => 'bridge_id']);
    }

}
