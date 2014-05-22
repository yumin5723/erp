<?php
namespace backend\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

class Species extends ActiveRecord
{

    public static function tableName()
    {
        return 'new_species';
    }
    public function rules(){
        return [
            ['id','safe'],
            ['spe_id','required','message'=>'犬种ID不允许为空！'],
            ['spe_id','integer','message'=>'犬种ID必须为整数！'],
            ['spe_title','required','message'=>'关键词不允许为空！'],
        ];
    }
    public function getAllSpeciesTitle(){
        if(!$this->emptyData()){
            $command = static::getDogDb()->createCommand("select spe_id,spe_domain_title from dog_species");
            $data = $command->queryAll();
            if(!empty($data)){
                foreach($data as $k=>$v){
                    $rs = []; $count =0;
                    if(!empty($v['spe_domain_title'])){
                        $rs = explode('-',$v['spe_domain_title']);
                        $count = count($rs);
                        // $ns = $this->getSpecieId($v['spe_id']);
                        if($count>0){
                            $this->saveSpecies($rs,$v['spe_id']);    
                        }
                    }
                }
            }
        }
    }

    public function saveSpecies($data,$spe_id){
        foreach($data as $vs){
            $model = new Species;
            $model->spe_id = $spe_id;
            $model->spe_title = $vs;
            $model->save(); 
        }
    }

    public function getAllData(){
        $str = $cstr = $dstr ='';
        if(!empty($_GET['Species']['spe_id'])){
            $str = " spe_id='{$_GET['Species']['spe_id']}'";
        }
        if(!empty($_GET['Species']['spe_title'])){
            $cstr = " spe_title like '%{$_GET['Species']['spe_title']}%'";
        }
        if(!empty($_GET['Species']['id'])){
            $dstr = " id='{$_GET['Species']['id']}'";
        }
        $provider = new ActiveDataProvider([
              'query' => static::find()
                          ->andWhere($str)
                          ->andWhere($cstr)
                          ->andWhere($dstr)
                         ->orderby('spe_id asc'),
            'sort' => [
                'attributes' => ['id','spe_id'],
            ],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);
        return $provider;
    }

    public function emptyData(){
        $data = static::find()->count();
        return $data;
    }

    public function getSpecieId($spe_id){
        $r = static::find()
            ->where(['spe_id'=>$spe_id])
            ->count();
        return $r;
    }

    public static function getDogDb(){
        return \Yii::$app->get("dogdb");
    }
    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID：',
            'spe_id' => '犬种ID(只为整数)：',
            'spe_title' => '关键词：',
        ];
    }
    /**
     * @return 
     */
    public function deleteSpe($id){
        $model = static::findOne($id);
        $model->delete();
        if($model->delete()){
            return true;
        }
        return false;
    }

    public function updateAttrs($attributes){
        $attrs = array();
        if (!empty($attributes['spe_id']) && $attributes['spe_id'] != $this->spe_id) {
            $attrs[] = 'spe_id';
            $this->spe_id = $attributes['spe_id'];
        }
         if (!empty($attributes['spe_title']) && $attributes['spe_title'] != $this->spe_title) {
            $attrs[] = 'spe_title';
            $this->spe_title = $attributes['spe_title'];
        }
        if ($this->validate($attrs)) {
            return $this->save(false);
        } else {
            return false;
        }
	}
	/**
	 * [getSpeIdByKeywords description]
	 * @param  [type] $keywords [description]
	 * @return [type]           [description]
	 */
	public function getSpeIdByKeywords($keywords){
		$result = self::find()->where("spe_title LIKE '%$keywords%'")->one();
		if(empty($result)){
			//rand dog species
			$array = ["35","60","65","45"];
			$dog_id = $array[array_rand($array)];
		}else{
			$dog_id = $result->spe_id;
		}
		$ret = [];
		$commond = static::getDogDb()->createCommand("select spe_nickname from dog_species where spe_id=".$dog_id);
		$data = $commond->queryOne();
		$ret['dogname'] = $data['spe_nickname'];
		$ret['image'] = "http://img1.goumin.com/attachments/spe/{$dog_id}.jpg";
		$ret['url'] = "http://www.goumin.com/species/".$dog_id;
		return $ret;
	}
}	
