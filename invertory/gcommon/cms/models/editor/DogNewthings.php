<?php
namespace gcommon\cms\models\editor;

use Yii;
use yii\data\ActiveDataProvider;
use gcommon\cms\components\CmsActiveRecord;
use yii\db\Query;

class DogNewthings extends CmsActiveRecord{
    public $duration = 86400;
    public static function tableName(){
        return "dog_newthings";
    }

    public function rules(){
        return [
            ['id','safe'],
            [['spe_id','forum_id'],'required'],
        ];
    }

    public function createNewthings($params){
        $model = new DogNewthings;
        if($params['DogNewthings']['spe_id']){
            $spe_id = static::findOne(['spe_id'=>$params['DogNewthings']['spe_id']]);
            if($spe_id){
                return false;
            }
            $forum_id = static::findOne(['forum_id'=>$params['DogNewthings']['forum_id']]);
            if($forum_id){
                return false;
            }
        }
        if($model->load($params) && $model->save()){
            return true;
        }
        return false;
    }

    public function getAllTings(){
        $provider = new ActiveDataProvider([
            'query' => static::find(),
            'sort'  => [
                'attributes' => ['id'],
            ],
            'pagination' =>[
                'pageSize' =>30,
            ],

        ]);
        return $provider;
    }

    public function getSpecies(){
        $cache_id = "species";
        // $datas = \Yii::$app->cache->get($cache_id);
        // if($datas == false){
            $command = static::getDogDb()->createCommand("select spe_name_s,spe_id from dog_species");
            $rs = $command->queryAll();
            $datas = [];
            if($rs){
                foreach ($rs as $key => $v) {
                    $datas[$v['spe_id']] = $v['spe_name_s'];
                }
            }
            // Yii::$app->cache->set($cache_id,$datas,$this->duration);
        // }
        return $datas;
    }

    public function getSpeName(){
        return function ($data){
            $command = static::getDogDb()->createCommand("select spe_name_s from dog_species where spe_id={$data->spe_id}");
            $spes = $command->queryOne();
            return $spes['spe_name_s'];
        };
    }

    public function getSName($spe_id){
        $command = static::getDogDb()->createCommand("select spe_name_s from dog_species where spe_id={$spe_id}");
        $spes = $command->queryOne();
        return $spes['spe_name_s'];
    }


    public function getDogDb(){
        return \Yii::$app->get("dogdb");
    }


    public function updateThing($id){
        $post = static::findOne($id);
        if (!$post) {
            return false;
        }
        if (\Yii::$app->request->isPost) {
            $params = Yii::$app->request->post();
            $forum_id = static::find()->where("forum_id={$params['DogNewthings']['forum_id']} and spe_id!={$post->spe_id}")->one();
            if($forum_id){
                return '2';
            }
            $post->load($params);
            if ($post->save()) {
                return true;
            }
        }
        return false;
    }
    public function attributeLabels(){
        return [
            'spe_id'=>'犬种ID',
            'forum_id'=>'版块ID',
        ];
    }

    public function delThing($id){
        $model = static::findOne($id);
        $model->delete();
    }
    /**
     * [getThingBySpeId description]
     * @param  [type] $spe_id [description]
     * @return [type]         [description]
     */
    public function getThingBySpeId($spe_id,$num = 5){
        $data = DogNewthings::find()->where(['spe_id'=>$spe_id])->orderby("id desc")->limit($num)->all();
        $rs = [];
        if($data){
            foreach ($data as $key => $v) {
                $rs[$key]['forum_id'] = $v['forum_id'];
                $rs[$key]['spe_id'] = $v['spe_id'];
            }
        }
        return $rs;
    }

    public function getPostsBySpeId($spe_id){
        $result = $this->getForumIdBySpeId($spe_id);
        $fid = $result ? $result : "148";
        $query = new Query;
        $data = $query->select("tid,subject")
              ->from("pre_forum_thread")
              ->where("digest = 1 AND fid={$fid}")
              ->orderBy("dateline DESC")
              ->limit("5")
              ->all(Yii::$app->get("dogdb"));
        $ret = [];
        foreach($data as $key=>$value){
            $ret[$key]['link'] = "http://bbs.goumin.com/thread-".$value['tid']."-1-1.html";
            $ret[$key]['title'] = $value['subject'];
        }
        return $ret;
    }

    public function getForumIdBySpeId($spe_id){
        $query = new Query;
        $data = $query->select("forum_id")->from(self::tableName())->where(['spe_id'=>$spe_id])->one(Yii::$app->get("cmsdb"));
        if ($data) {
            return $data["forum_id"];
        }
        return false;
    }
}
