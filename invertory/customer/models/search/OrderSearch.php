<?php

namespace customer\models\search;

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
            [['goods_code', 'goods_quantity','goods_active','storeroom_id','recipients','recipients_address','recipients_contact','status'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Order::find()->where(['is_del'=>Order::ORDER_IS_NOT_DEL,'owner_id'=>Yii::$app->user->id])->orderBy(['id'=>SORT_DESC]);

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
        ]);
        // $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
    public function searchByPost($orderid){
        $query = Order::find()->where(['viewid'=>$orderid,'is_del'=>Order::ORDER_IS_NOT_DEL]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        return $dataProvider;
    }
}
