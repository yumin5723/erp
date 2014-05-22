<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Forum;

/**
 * PostSearch represents the model behind the search form about `backend\models\Post`.
 */
class ForumSearch extends Forum
{
    public function rules()
    {
        return [
            [['fid'], 'integer'],
            [['name','status'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Forum::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'fid' => $this->fid,
            'name' => $this->name,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere('status != 1');
        return $dataProvider;
    }
}
