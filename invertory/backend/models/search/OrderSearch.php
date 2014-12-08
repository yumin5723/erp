<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Order;

/**
 * PostSearch represents the model behind the search form about `backend\models\Post`.
 */
class OrderSearch extends Order
{
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['goods_code','viewid','goods_quantity','goods_active','storeroom_id','recipients','recipients_address','recipients_contact','status'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params,$storeroom_id)
    {
        $query = Order::find()->where(['is_del'=>Order::ORDER_IS_NOT_DEL])->orderBy(['id'=>SORT_DESC]);
        if($storeroom_id != self::BIGEST_STOREROOM_ID){
            $query->andWhere(['storeroom_id'=>$storeroom_id]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'goods_code' => $this->goods_code,
            'status'=>$this->status,
            'viewid'=>$this->viewid,
        ]);
        // $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
    public function searchByPost($orderid,$storeroom_id){
        $query = Order::find()->where(['viewid'=>$orderid,'is_del'=>Order::ORDER_IS_NOT_DEL]);
        if($storeroom_id != self::BIGEST_STOREROOM_ID){
            $query->andWhere(['storeroom_id'=>$storeroom_id]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        return $dataProvider;
    }
}
