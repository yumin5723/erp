<?php
namespace common\models\db;
use Yii;
use gcommon\cms\models\Db;
use yii\db\Query;
class Bbsblock extends Db{
	protected $_dbType = "bbsblock";

	public function getDbType(){
		return $this->_dbType;
	}
	/**
	 * get bbs focus data
	 */
	public function getData($params = null){
		$params = explode("%", $params);
		$column = $params[0];
		$bbs_block_id = $params[1];
		$query = new Query;
		$results = $query
				->select("$column")
				->from(self::getBlockTableName())
				->where(['bid'=>$bbs_block_id])
				->all(self::getDb());
		if(in_array("fields", explode(",", $column))){
			foreach($results as $key=>$result){
				if(strpos($result['url'],'http://') === false){
	                $ret[$key]['url'] = "http://bbs.goumin.com/".$result['url'];
	            }else{
	                $ret[$key]['url'] = $result['url'];
	            }
				$ret[$key]['title'] = $result['title'];
				$fields = unserialize($result['fields']);
				$ret[$key]['fields'] = $fields;
			}
			return $ret;
		}
		return $results;
	}
	/**
	 * get database
	 * @return [type] [description]
	 */
	public static function getDb(){
		return \Yii::$app->get('dogdb');
	}
	/**
	 * find from name
	 * @return [type] [description]
	 */
	public static function getBlockTableName(){
		return 'pre_common_block_item';
	}
}