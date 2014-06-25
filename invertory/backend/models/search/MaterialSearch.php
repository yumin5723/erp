<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Material;
use backend\models\Owner;

/**
 * PostSearch represents the model behind the search form about `backend\models\Post`.
 */
class MaterialSearch extends Material
{
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['code', 'name','english_name','project_id'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Material::find()->orderBy(['id'=>SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'english_name' => $this->english_name,
        ]);

        $query->andFilterWhere(['owner_id'=>$this->owner_id]);

        return $dataProvider;
    }
}
