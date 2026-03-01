<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\BridgeTables;

/**
 * BridgeTablesSearch represents the model behind the search form of `app\models\BridgeTables`.
 */
class BridgeTablesSearch extends BridgeTables
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'bridge_id', 'source_table_name', 'target_table_name'], 'safe'],
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
        $query = BridgeTables::find();

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
            ->andFilterWhere(['like', 'bridge_id', $this->bridge_id])
            ->andFilterWhere(['like', 'source_table_name', $this->source_table_name])
            ->andFilterWhere(['like', 'target_table_name', $this->target_table_name]);

        return $dataProvider;
    }
}
