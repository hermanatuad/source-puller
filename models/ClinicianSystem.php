<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "clinician_system".
 *
 * @property int $id
 * @property string $clinician_id
 * @property string $clinician_reference
 * @property string $system_code
 */
class ClinicianSystem extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'clinician_system';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'clinician_id', 'clinician_reference', 'system_code'], 'required'],
            [['id'], 'integer'],
            [['clinician_id', 'system_code'], 'string', 'max' => 20],
            [['clinician_reference'], 'string', 'max' => 255],
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
            'clinician_reference' => 'Clinician Reference',
            'system_code' => 'System Code',
        ];
    }

}
