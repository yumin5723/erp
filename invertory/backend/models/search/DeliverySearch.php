<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Delivery;

/**
 * PostSearch represents the model behind the search form about `backend\models\Post`.
 */
class DeliverySearch extends Delivery
{
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name','contact','address','phone','city'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Delivery::find()->orderBy(['id'=>SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'name' => $this->name,
            'contact'=>$this->contact,
            'city'=>$this->city,
        ]);

        return $dataProvider;
    }
}
