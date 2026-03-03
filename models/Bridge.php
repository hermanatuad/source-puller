<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bridge".
 *
 * @property string $id
 * @property string $system_code
 * @property string $bridge_table_source
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property BridgeColumn[] $bridgeColumns
 * @property System $systemCode
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
            [['id', 'system_code', 'bridge_table_source', 'status'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['id'], 'string', 'max' => 36],
            [['system_code', 'status'], 'string', 'max' => 20],
            [['bridge_table_source'], 'string', 'max' => 255],
            [['id'], 'unique'],
            [['system_code'], 'exist', 'skipOnError' => true, 'targetClass' => System::class, 'targetAttribute' => ['system_code' => 'system_code']],
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
            'bridge_table_source' => 'Bridge Table Source',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[BridgeColumns]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBridgeColumns()
    {
        return $this->hasMany(BridgeColumn::class, ['bridge_id' => 'id']);
    }

    /**
     * Gets query for [[SystemCode]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSystemCode()
    {
        return $this->hasOne(System::class, ['system_code' => 'system_code']);
    }

}
