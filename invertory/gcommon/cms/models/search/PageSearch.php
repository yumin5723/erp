<?php

namespace gcommon\cms\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use gcommon\cms\models\Page;

/**
 * PostSearch represents the model behind the search form about `backend\models\Post`.
 */
class PageSearch extends Page
{
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['path', 'name'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Page::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'name' => $this->name,
            'path' => $this->path,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
