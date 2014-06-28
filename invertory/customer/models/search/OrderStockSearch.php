<?php

namespace customer\models\search;

use Yii;
use yii\base\Model;
use yii\data\ArrayDataProvider;
use customer\models\Stock;
use customer\models\Owner;

/**
 * PostSearch represents the model behind the search form about `backend\models\Post`.
 */
class OrderStockSearch extends Stock
{

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public static function search($owner_id,$storeroom_id)
    {
        $results = Stock::find()->where(['owner_id'=>$owner_id,'storeroom_id'=>$storeroom_id])->orderBy(['id'=>SORT_DESC])->all();
        $arr = [];
        if($results){
            foreach($results as $key=>$v){
                $arr[$v->material['code']]['code'] = $v->material['code'];
                $arr[$v->material['code']]['name'] = $v->material['name'];
                $arr[$v->material['code']]['count'] = $v->stocktotal->total;
            }

        }
        $provider = new ArrayDataProvider([
            'allModels' => $arr,
        ]);

        // if (!($this->load($params))) {
        //     return $provider;
        // }
        return $provider;
    }
    public function getLink(){
        return '
            return \yii\helpers\Html::input("text","selection[$model->code][]");
        ';
    }
}
