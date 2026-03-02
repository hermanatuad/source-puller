<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\AbstractionColumn;

/**
 * AbstractionColumnSearch represents the model behind the search form of `app\models\AbstractionColumn`.
 */
class AbstractionColumnSearch extends AbstractionColumn
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'abstraction_id', 'column_type', 'column_warehouse', 'description', 'created_at', 'updated_at'], 'safe'],
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
        $query = AbstractionColumn::find();

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
        $query->andFilterWhere([
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'abstraction_id', $this->abstraction_id])
            ->andFilterWhere(['like', 'column_type', $this->column_type])
            ->andFilterWhere(['like', 'column_warehouse', $this->column_warehouse])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
