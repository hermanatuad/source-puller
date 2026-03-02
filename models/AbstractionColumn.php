<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "abstraction_column".
 *
 * @property string $id
 * @property string $abstraction_id
 * @property string $column_type
 * @property string $column_warehouse
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Abstraction $abstraction
 */
class AbstractionColumn extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'abstraction_column';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'abstraction_id', 'column_type', 'column_warehouse', 'description'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['id', 'abstraction_id'], 'string', 'max' => 36],
            [['column_type'], 'string', 'max' => 20],
            [['column_warehouse', 'description'], 'string', 'max' => 255],
            [['id'], 'unique'],
            [['abstraction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Abstraction::class, 'targetAttribute' => ['abstraction_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'abstraction_id' => 'Abstraction ID',
            'column_type' => 'Column Type',
            'column_warehouse' => 'Column Warehouse',
            'description' => 'Description',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Abstraction]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAbstraction()
    {
        return $this->hasOne(Abstraction::class, ['id' => 'abstraction_id']);
    }

}
