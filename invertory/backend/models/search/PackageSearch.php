<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Package;

/**
 * PostSearch represents the model behind the search form about `backend\models\Post`.
 */
class PackageSearch extends Package
{
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['num', 'actual_weight','throw_weight','volume','method','trunk','delivery','price'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Package::find()->orderBy(['id'=>SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        // $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
