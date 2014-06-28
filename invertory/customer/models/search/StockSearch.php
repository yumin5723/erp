<?php

namespace customer\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use customer\models\Stock;
use customer\models\Owner;

/**
 * PostSearch represents the model behind the search form about `backend\models\Post`.
 */
class StockSearch extends Stock
{
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['material_id','project_id','storeroom_id','increase','owner_id'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Stock::find()->orderBy(['id'=>SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        if(isset($this->owner_id) && !empty($this->owner_id)){
            $owner = Owner::find()->where(['english_name'=>$this->owner_id])->one();
            if(!empty($owner)){
                $this->owner_id = $owner->id;
            }
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'material_id' => $this->material_id,
        ]);

        return $dataProvider;
    }
    public function searchByPost($material_id,$increase){
        $query = Stock::find()->where(['material_id'=>$material_id,'increase'=>$increase])->orderBy(['id'=>SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        return $dataProvider;
    }
}
