<?php
namespace gcommon\cms\models\editor;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use gcommon\cms\components\CmsActiveRecord;
use yii\db\Query;

class NewDogs extends CmsActiveRecord{

	public $dog_headid;
	public static function tableName(){
		return "new_dog";
	}

	public function rules(){
		return [
			[['dog_id','dog_name','dog_userid','dog_headid'],'required'],
		];
	}

	public function getAllDogs(){
		$provider = new ActiveDataProvider([
			'query' => static::find(),
			'sort'  => [
				'attributes' => ['id'],
			],
			'pagination' =>[
				'pageSize' =>30,
			],
		]);		
	}

	public function getAllOldData(){
		$dog_ids = $this->getAllIDs();
		if($dog_ids){
			$dog_ids = " and dog_id not in($dog_ids)";
		}
		$command = static::getDogDb()->createCommand("select dog_id,dog_name,dog_userid,dog_headid from dog_doginfo where dog_headid != 0 and dog_cdate < unix_timestamp()-600 $dog_ids
		  order by dog_cdate desc limit 1000");
		$rs = $command->queryAll();
		  $provider = new ArrayDataProvider([
		      'allModels' => $rs,
		      'sort' => [
		          'attributes' => ['dog_id', 'dog_name', 'dog_userid'],
		      ],
		      'pagination' => [
		          'pageSize' => 10,
		      ],
		  ]);
		  return $provider;
	}

	public  function getIndexPetsHead(){
		$rs = NewDogs::find()->orderby("dog_id desc")->limit(16)->all();
		$ids = '';
		if($rs){
			foreach ($rs as $value) {
				$ids .= $value['dog_id'].',';
			}
			$ids = trim($ids,',');
		}
		$data = static::getDogDb()->createCommand("select d.dog_id,d.dog_name,d.dog_headid,i.head_id,d.dog_userid,i.head_cdate,i.head_fileext from dog_doginfo d,dog_head_image i where d.dog_headid=i.head_id and d.dog_id in ($ids)")->queryAll();
		return $data;
	}

	public function getHead(){
		return function ($data){
			return '<img src="http://www.goumin.com/api/getHeadImage.php?head_id='.$data['dog_userid'].'"/>';
		};
	}
	public static function getAhref(){
		return function ($data){
			return '<a href="/cms/editor/push-dog?dog_id='.$data['dog_id'].'">推荐到首页</a>';
		};
	}
	public function getAllNewData(){
		$data = static::find()->orderby("dog_id desc")->all();
		return $data;
	}

	public function getAllIDs(){
		$data = static::find()->select("dog_id")->all();
		$ids = '';
		if($data){
			foreach ($data as $key => $v) {
				$ids .= $v['dog_id'].',';
			}
		}
		$ids = trim($ids,',');
		return $ids;
	}


	public function getDogDb(){
		return \Yii::$app->get("dogdb");
	}


	public function pushData($dog_id){
		$command = static::getDogDb()->createCommand("select dog_id,dog_name,dog_userid,dog_headid from dog_doginfo where dog_id={$dog_id}");
		$doginfo = $command->queryOne();
		$model = new NewDogs;
		if(!NewDogs::findOne(['dog_id'=>$dog_id])){
			if($model->load($doginfo) && $model->save()){
				return true;
			}
		}
		return false;
	}


	public function cancelData($dog_id){
		$model = NewDogs::findOne(['dog_id'=>$dog_id]);
		if($model){
			$model->delete();
		}
	}


	public function load($data, $formName = null)
	{
		$this->setAttributes($data);
        return true;
	}


	public function getDogList(){
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
}