<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bridge".
 *
 * @property string $id
 * @property string $system_code
 * @property string $bridge_type
 * @property string $bridge_source
 * @property string $bridge_target
 * @property string $created_at
 * @property string $updated_at
 *
 * @property BridgeLog[] $bridgeLogs
 * @property BridgeTables[] $bridgeTables
 */
class Bridge extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bridge';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'system_code', 'bridge_type', 'bridge_source', 'bridge_target'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['id'], 'string', 'max' => 36],
            [['system_code', 'bridge_type'], 'string', 'max' => 20],
            [['bridge_source', 'bridge_target'], 'string', 'max' => 50],
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
            'bridge_type' => 'Bridge Type',
            'bridge_source' => 'Bridge Source',
            'bridge_target' => 'Bridge Target',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[BridgeLogs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBridgeLogs()
    {
        return $this->hasMany(BridgeLog::class, ['bridge_id' => 'id']);
    }

    /**
     * Gets query for [[BridgeTables]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBridgeTables()
    {
        return $this->hasMany(BridgeTables::class, ['bridge_id' => 'id']);
    }

}
