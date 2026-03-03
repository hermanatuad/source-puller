<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bridge_column".
 *
 * @property string $id
 * @property string $bridge_id
 * @property string $source_columnn_name
 * @property string $target_columnn_name
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Bridge $bridge
 */
class BridgeColumn extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bridge_column';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'bridge_id', 'source_columnn_name', 'target_columnn_name'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['id', 'bridge_id'], 'string', 'max' => 36],
            [['source_columnn_name', 'target_columnn_name'], 'string', 'max' => 255],
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
            'source_columnn_name' => 'Source Columnn Name',
            'target_columnn_name' => 'Target Columnn Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
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
