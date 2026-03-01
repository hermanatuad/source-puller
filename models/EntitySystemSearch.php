<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\EntitySystem;

/**
 * EntitySystemSearch represents the model behind the search form of `app\models\EntitySystem`.
 */
class EntitySystemSearch extends EntitySystem
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['entity_id', 'entity_reference', 'system_code', 'created_at_data', 'updated_at_data', 'created_at', 'updated_at'], 'safe'],
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
        $query = EntitySystem::find();

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
            'id' => $this->id,
            'created_at_data' => $this->created_at_data,
            'updated_at_data' => $this->updated_at_data,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'entity_id', $this->entity_id])
            ->andFilterWhere(['like', 'entity_reference', $this->entity_reference])
            ->andFilterWhere(['like', 'system_code', $this->system_code]);

        return $dataProvider;
    }
}
