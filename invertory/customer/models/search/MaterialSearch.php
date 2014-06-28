<?php

namespace customer\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use customer\models\Material;
use customer\models\Owner;

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
        //customer can see only hisself material
        $query = Material::find()->where(['owner_id'=>Yii::$app->user->id])->orderBy(['id'=>SORT_DESC]);

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
