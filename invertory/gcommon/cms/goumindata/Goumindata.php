<?php
namespace gcommon\cms\goumindata;
use Yii;
use yii\db\Query;
use gcommon\cms\components\GData;
use gcommon\cms\models\editor\DogNewthings;
class Goumindata extends GData{
	const FOCUS_BLOCK_ID = 96;

	public static function getAllDogNewThings(){
		$model = new DogNewthings;
		$data = DogNewthings::find()->orderby("id desc")->limit(5)->all();
		$rs = [];
		if($data){
			foreach ($data as $key => $v) {
				$rs[$key]['title'] = $v['title'];
				$rs[$key]['link'] = $v['link'];
				$rs[$key]['spe_id'] = $v['spe_id'];
				$rs[$key]['spe_name'] = $model->getSName($v['spe_id']);
			}
		}
		return $rs;
	}
}