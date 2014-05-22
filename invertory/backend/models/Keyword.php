<?php
namespace backend\models;

use Yii;
use yii\base\Model;
use yii\redis\Cache;
use api\components\RedisConnection;


class Keyword extends Model
{
	public $keywords = "key_words";

	public function getKeyWordList(){
		if(Yii::$app->cache->exists($this->keywords)){
			return Yii::$app->cache->getSmembers($this->keywords);
		} else {
			$rootDir = dirname(dirname(__DIR__));
			$file = $rootDir.'/api/config/keywords.txt';
			$handle = @fopen($file, "r");
			if ($handle) {
				while (($buffer = fgets($handle)) !== false) {
					Yii::$app->cache->sadd($this->keywords,trim($buffer));
				}
				fclose($handle);
			}
			return Yii::$app->cache->getSmembers($this->keywords);
		}
	}

	public function delKeyword($word){
		return Yii::$app->cache->sRem($this->keywords,$word);
	}

	public function createNewKeyword($word){
		if(Yii::$app->cache->sIsmember($this->keywords,$word)){
			return false;
		} else {
			$result = Yii::$app->cache->sadd($this->keywords,$word);
			return true;
		}
	}
}