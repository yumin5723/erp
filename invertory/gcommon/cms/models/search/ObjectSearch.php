<?php

namespace gcommon\cms\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use gcommon\cms\models\Object;

/**
 * PostSearch represents the model behind the search form about `backend\models\Post`.
 */
class ObjectSearch extends Object
{
    public function rules()
    {
        return [
            [['object_id'], 'integer'],
            [['object_author', 'object_date','object_title','object_name','object_status'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Object::find()->orderBy(['object_id'=>SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'object_id' => $this->object_id,
            'object_author' => $this->object_author,
            'object_date' => $this->object_date,
            'object_title' => $this->object_title,
            //'object_name' => $this->object_name,
            'object_status' => $this->object_status,
        ]);
        $query->andFilterWhere(['like', 'object_name', $this->object_name]);
        return $dataProvider;
    }
}
