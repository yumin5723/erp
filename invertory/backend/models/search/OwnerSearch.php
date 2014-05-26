<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Owner;

/**
 * PostSearch represents the model behind the search form about `backend\models\Post`.
 */
class OwnerSearch extends Owner
{
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['english_name', 'email','phone','tell'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Owner::find()->orderBy(['id'=>SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'english_name' => $this->english_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'tell' => $this->tell,
        ]);

        // $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
