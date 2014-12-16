<?php

namespace gcommon\cms\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use gcommon\cms\models\Tag;

/**
 * PostSearch represents the model behind the search form about `backend\models\Post`.
 */
class TagSearch extends Tag
{
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name', 'url'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Tag::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'name' => $this->name,
            'url' => $this->url,
        ]);

        return $dataProvider;
    }
}
