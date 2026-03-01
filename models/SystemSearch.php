<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\System;

/**
 * SystemSearch represents the model behind the search form of `app\models\System`.
 */
class SystemSearch extends System
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'system_code', 'system_name', 'description'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {
        $query = System::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'system_code', $this->system_code])
            ->andFilterWhere(['like', 'system_name', $this->system_name])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
