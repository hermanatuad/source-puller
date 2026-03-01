<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "clinician_affiliation".
 *
 * @property int $id
 * @property string $clinician_id
 * @property string $affiliation_code
 */
class ClinicianAffiliation extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'clinician_affiliation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'clinician_id', 'affiliation_code'], 'required'],
            [['id'], 'integer'],
            [['clinician_id', 'affiliation_code'], 'string', 'max' => 20],
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
            'affiliation_code' => 'Affiliation Code',
        ];
    }

}
