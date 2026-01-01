<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "students".
 *
 * @property int $student_id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $email
 * @property string|null $enrollment_date
 * @property float|null $gpa
 */
class Students extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'students';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'enrollment_date', 'gpa'], 'default', 'value' => null],
            [['first_name', 'last_name'], 'required'],
            [['enrollment_date'], 'safe'],
            [['gpa'], 'number'],
            [['first_name', 'last_name'], 'string', 'max' => 30],
            [['email'], 'string', 'max' => 100],
            [['email'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'student_id' => 'Student ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email',
            'enrollment_date' => 'Enrollment Date',
            'gpa' => 'Gpa',
        ];
    }

}
