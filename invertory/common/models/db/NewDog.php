<?php
namespace common\models\db;
use Yii;
use gcommon\cms\models\Db;
use gcommon\cms\models\editor\NewDogs;
use gcommon\cms\models\editor\NewPhotos;
class NewDog extends Db{
	protected $_dbType = "newdog";

	public function getDbType(){
		return $this->_dbType;
	}
	/**
	 * get bbs focus data
	 */
	public function getData($params = null){
		$model = new NewDogs;
		$data = $model->getIndexPetsHead();
		$results = [];
		if($data){
			$model = new NewPhotos;
			foreach ($data as $key => $v) {
				$results[$key]['dog_id'] = $v['dog_id'];
				$results[$key]['dog_name'] = $v['dog_name'];
				$results[$key]['dog_userid'] = $v['dog_userid'];
				$results[$key]['dog_header'] = $model->getDogHeadUrl($v['head_id'],$v['head_fileext'],$v['head_cdate']);
			}
		}
		return $results;
	}

}