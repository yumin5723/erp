<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Storeroom;

/**
 * PostSearch represents the model behind the search form about `backend\models\Post`.
 */
class StoreroomSearch extends Storeroom
{
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name', 'address','level','contact','phone'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Storeroom::find()->orderBy(['id'=>SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'level' => $this->level,
            'contact' => $this->contact,
            'phone' => $this->phone,
        ]);

        $query->andFilterWhere(['like', 'address', $this->address]);

        return $dataProvider;
    }
}
