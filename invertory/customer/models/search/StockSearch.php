<?php

namespace customer\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Stock;
use backend\models\Owner;
use backend\models\Material;

/**
 * PostSearch represents the model behind the search form about `backend\models\Post`.
 */
class StockSearch extends Stock
{
    
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['material_id','increase'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        // $needData = ['id','owner_id','storeroom_id','material_id','forecast_quantity','actual_quantity','stock_time','delivery','increase','order_id'];
        $needData = ['material_id','storeroom_id','total'=>'sum(actual_quantity)'];
        $query = Stock::find()->select($needData)->with('material')->where(['owner_id'=>Yii::$app->user->id])->groupby(['storeroom_id','material_id'])->orderBy(['id'=>SORT_DESC]);

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
    public function searchList($params)
    {
        $query = Stock::find()->where(['owner_id'=>Yii::$app->user->id,'increase'=>Stock::IS_INCREASE])->orderBy(['id'=>SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        if(isset($this->material_id) && !empty($this->material_id)){
            $material = Material::find()->where(['code'=>$this->material_id])->one();
            if(!empty($material)){
                $this->material_id = $material->id;
            }
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'material_id' => $this->material_id,
        ]);

        return $dataProvider;
    }
    public function searchOutput($params)
    {
        $query = Stock::find()->where(['owner_id'=>Yii::$app->user->id,'increase'=>Stock::IS_NOT_INCREASE])->orderBy(['id'=>SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        if(isset($this->material_id) && !empty($this->material_id)){
            $material = Material::find()->where(['code'=>$this->material_id])->one();
            if(!empty($material)){
                $this->material_id = $material->id;
            }
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'material_id' => $this->material_id,
        ]);

        return $dataProvider;
    }
    public function searchDetail($params)
    {
        $needData = ['id','owner_id','storeroom_id','material_id','forecast_quantity','actual_quantity','stock_time','delivery','increase','order_id'];
        $query = Stock::find()->select($needData)->with('material')->distinct()->where(['owner_id'=>Yii::$app->user->id])->orderBy(['id'=>SORT_DESC]);

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
    public function getExportLink(){
        return ['0'=>'/stock/export?mid='.$this->material_id];
    }
    public function getOutputExportLink(){
        return ['0'=>'/stock/exportoutput?mid='.$this->material_id];
    }
    public function getStockLink(){
        return '
            return $model->stocktotal->total ."  ".\yii\helpers\Html::a("库存明细","/material/detail?StockSearch[material_id]={$model->material->id}");
        ';
    }
    public function getViewLink(){
        return '
            return \yii\helpers\Html::a("查看","/material/view?id={$model->material->id}",["target"=>"_blank"]);
        ';
    }
}
