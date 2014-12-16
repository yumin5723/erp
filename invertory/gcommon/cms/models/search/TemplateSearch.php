<?php

namespace gcommon\cms\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use gcommon\cms\models\Template;

/**
 * PostSearch represents the model behind the search form about `backend\models\Post`.
 */
class TemplateSearch extends Template
{
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['type', 'name'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Template::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'name' => $this->name,
        ]);

        $query->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
