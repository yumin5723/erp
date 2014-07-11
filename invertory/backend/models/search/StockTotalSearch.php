<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\StockTotal;

/**
 * PostSearch represents the model behind the search form about `backend\models\Post`.
 */
class StockTotalSearch extends StockTotal
{
    public function rules()
    {
        return [
            [['material_id','storeroom_id'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = StockTotal::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'material_id' => $this->material_id,
            'storeroom_id'=> $this->storeroom_id,
        ]);
        return $dataProvider;
    }
}
