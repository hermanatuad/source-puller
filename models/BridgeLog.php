<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bridge_log".
 *
 * @property string $id
 * @property string $bridge_id
 * @property string $log_message
 * @property string $created_at
 *
 * @property Bridge $bridge
 */
class BridgeLog extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bridge_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'bridge_id', 'log_message'], 'required'],
            [['log_message'], 'string'],
            [['created_at'], 'safe'],
            [['id', 'bridge_id'], 'string', 'max' => 36],
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
            'log_message' => 'Log Message',
            'created_at' => 'Created At',
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
