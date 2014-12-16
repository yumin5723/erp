<?php

namespace gcommon\cms\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use gcommon\cms\models\Block;

/**
 * PostSearch represents the model behind the search form about `backend\models\Post`.
 */
class BlockSearch extends Block
{
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['type', 'page_type','name'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Block::find();
        // if(\Yii::$app->user->id != 1){
        //     $query = Block::find()->where(['type'=>4]);
        // }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'page_type' => $this->page_type,
        ]);

        return $dataProvider;
    }
}
