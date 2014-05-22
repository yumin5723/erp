<?php

namespace backend\models\search;

use Yii;
use yii\data\ActiveDataProvider;
use backend\models\GoodsBack;

class GoodsSearch extends GoodsBack
{
    public function rules()
    {
        // only fields in rules() are searchable
        return [
            ['goods_id', 'integer'],
            ['goods_name', 'safe'],
        ];
    }


    public function search($params)
    {
        $query = GoodsSearch::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // load the seach form data and validate
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        // adjust the query by adding the filters
        $query->andFilterWhere(['goods_id' => $this->goods_id]);
        $query->andFilterWhere(['like', 'goods_name', $this->goods_name]);

        return $dataProvider;
    }
}

