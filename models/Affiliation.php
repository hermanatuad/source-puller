<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "affiliation".
 *
 * @property string $id
 * @property string $affiliation_code
 * @property string $affiliation_name
 * @property string $address
 */
class Affiliation extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'affiliation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'affiliation_code', 'affiliation_name', 'address'], 'required'],
            [['id'], 'string', 'max' => 36],
            [['affiliation_code'], 'string', 'max' => 20],
            [['affiliation_name', 'address'], 'string', 'max' => 255],
            [['affiliation_code'], 'unique'],
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
            'affiliation_code' => 'Affiliation Code',
            'affiliation_name' => 'Affiliation Name',
            'address' => 'Address',
        ];
    }

}
