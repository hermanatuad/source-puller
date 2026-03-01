<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "hospital".
 *
 * @property int $id
 * @property string $hospital_code
 * @property string $hospital_name
 * @property string $created_at
 * @property string $updated_at
 */
class Hospital extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hospital';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hospital_code', 'hospital_name', 'created_at', 'updated_at'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['hospital_code', 'hospital_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'hospital_code' => 'Hospital Code',
            'hospital_name' => 'Hospital Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

}
