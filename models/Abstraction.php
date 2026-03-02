<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "abstraction".
 *
 * @property string $id
 * @property string $table_name
 * @property string $table_warehouse
 * @property string $type
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 *
 * @property AbstractionColumn[] $abstractionColumns
 */
class Abstraction extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'abstraction';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'table_name', 'table_warehouse', 'type', 'description'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['id'], 'string', 'max' => 36],
            [['table_name', 'type'], 'string', 'max' => 20],
            [['table_warehouse', 'description'], 'string', 'max' => 255],
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
            'table_name' => 'Table Name',
            'table_warehouse' => 'Table Warehouse',
            'type' => 'Type',
            'description' => 'Description',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[AbstractionColumns]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAbstractionColumns()
    {
        return $this->hasMany(AbstractionColumn::class, ['abstraction_id' => 'id']);
    }

}
