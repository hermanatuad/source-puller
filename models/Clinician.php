<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "clinician".
 *
 * @property int $id
 * @property string $clinician_id
 * @property string $status
 */
class Clinician extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'clinician';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'clinician_id', 'status'], 'required'],
            [['id'], 'integer'],
            [['clinician_id', 'status'], 'string', 'max' => 20],
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
            'clinician_id' => 'Clinician ID',
            'status' => 'Status',
        ];
    }

}
