<?php
/**
* 
*/

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\db\Query;

class BoardstarController extends Controller {
	
	/**
	 * [actionDay description]
	 * @return [type] [description]
	 */
	public function actionDay(){
		$this->runDay();
	}

	/**
	 * [actionHour description]
	 * @return [type] [description]
	 */
	public function actionHour(){
		$this->runHour();
	}

	/**
	 * [runHour description]
	 * @return [type] [description]
	 */
	public function runHour(){
		$forums = $this->getForums();
		$forum_array = [];
		foreach ($forums as $key => $value) {
			$query = new Query;
			$reclist = $query->select("m.uid,m.username,count(pid) as posts")
							 ->from("pre_forum_post p, pre_forum_thread t, pre_ucenter_members m ")
							 ->where("p.authorid=m.uid and p.tid=t.tid and t.displayorder>=0 and p.dateline>unix_timestamp(from_unixtime(unix_timestamp()-3600,'%Y-%m-%d')) and p.fid={$value['fid']}")
							 ->groupBy("m.uid")
							 ->orderBy("posts desc")
							 ->limit(10)
							 ->all(Yii::$app->get("dogdb"));
			$forum_array[$key]["fid"] = $value["fid"];
			$forum_array[$key]["name"] = $value["name"];
			$forum_array[$key]["total"] = $this->getForumTotalForHour($value["fid"]);
			$forum_array[$key]["star_top10"] = $reclist;
		}
		$data = json_encode($forum_array,JSON_UNESCAPED_UNICODE);
		$this->saveDataHour($data);
	}
	/**
	 * [runDay description]
	 * @return [type] [description]
	 */
	public function runDay(){
		$forums = $this->getForums();
		$forum_array = [];
		foreach($forums as $key=>$value) {
			$query = new Query;
			$reclist = $query->select("m.uid,m.username,count(pid) as posts")
						     ->from("pre_forum_post p,pre_forum_thread t,pre_ucenter_members m")
						     ->where("p.tid=t.tid and t.displayorder>=0 and p.authorid=m.uid and p.dateline>unix_timestamp(from_unixtime(unix_timestamp()-24*3600,'%Y-%m-1')) and p.fid=".$value["fid"])
						     ->groupBy("m.uid")
						     ->orderBy("posts desc")
						     ->limit("10")
						     ->all(Yii::$app->get("dogdb"));
			$forum_array[$key]["fid"] = $value["fid"];
			$forum_array[$key]["name"] = $value["name"];
			$forum_array[$key]["total"] = $this->getForumTotal($value["fid"]);
			$forum_array[$key]["star_top10"] = $reclist;
		}
		$data = json_encode($forum_array,JSON_UNESCAPED_UNICODE);
		$this->saveData($data);
	}

	/**
	 * [saveData description]
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function saveData($data){
		$query = new Query;
		$result = $query->select("set_no")
			  ->from("dog_setting_daily")
			  ->where("set_id='FORUM_BOARD_STAR_LIST_MON' and set_year=year(from_unixtime(unix_timestamp()-24*3600,'%Y-%m-1')) and set_mon=month(from_unixtime(unix_timestamp()-24*3600,'%Y-%m-1')) and set_day=1")
			  ->one(Yii::$app->get("dogdb"));
		if ($result) {
			$ret = static::getDb("dogdb")->createCommand("update dog_setting_daily set set_value='{$data}' where set_no={$result['set_no']}")->execute();
		} else {
			$ret = static::getDb("dogdb")->createCommand("insert into dog_setting_daily(set_year, set_mon, set_day, set_id, set_value) values(year(from_unixtime(unix_timestamp()-24*3600,'%Y-%m-1')), month(from_unixtime(unix_timestamp()-24*3600,'%Y-%m-1')), 1, 'FORUM_BOARD_STAR_LIST_MON', '$data')")->execute();
		}
		return $ret;
	}

	/**
	 * [saveDataHour description]
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function saveDataHour($data){
		$query = new Query;
		$result = $query->select("set_no")
			  ->from("dog_setting_daily")
			  ->where("set_id='FORUM_BOARD_STAR_LIST' and set_year=year(from_unixtime(unix_timestamp()-3600,'%Y-%m-%d')) and set_mon=month(from_unixtime(unix_timestamp()-3600,'%Y-%m-%d')) and set_day=day(from_unixtime(unix_timestamp()-3600,'%Y-%m-%d'))")
			  ->one(Yii::$app->get("dogdb"));
		if ($result) {
			$ret = static::getDb("dogdb")->createCommand("update dog_setting_daily set set_value='{$data}' where set_no={$result['set_no']}")->execute();
		} else {
			$ret = static::getDb("dogdb")->createCommand("insert into dog_setting_daily(set_year, set_mon, set_day, set_id, set_value) values(year(from_unixtime(unix_timestamp()-3600,'%Y-%m-%d')), month(from_unixtime(unix_timestamp()-3600,'%Y-%m-%d')), day(from_unixtime(unix_timestamp()-3600,'%Y-%m-%d')), 'FORUM_BOARD_STAR_LIST', '$data')")->execute();
		}
		return $ret;
	}

	/**
	 * [getForumTotal description]
	 * @param  [type] $fid [description]
	 * @return [type]      [description]
	 */
	public function getForumTotal($fid){
		$query = new Query;
		$result = $query->select("count(*) AS num")
			  ->from("pre_forum_post p,pre_forum_thread t")
			  ->where("p.tid=t.tid and t.displayorder>=0 and p.dateline>unix_timestamp(from_unixtime(unix_timestamp()-24*3600,'%Y-%m-1')) and p.fid={$fid}")
			  ->one(Yii::$app->get("dogdb"));
	    return $result["num"];
	}

	/**
	 * [getForumTotalForHour description]
	 * @param  [type] $fid [description]
	 * @return [type]      [description]
	 */
	public function getForumTotalForHour($fid){
		$query = new Query;
		$result = $query->select("count(*) AS num")
			  ->from("pre_forum_post p,pre_forum_thread t")
			  ->where("p.tid=t.tid and t.displayorder>=0 and p.dateline>unix_timestamp(from_unixtime(unix_timestamp()-3600,'%Y-%m-%d')) and p.fid={$fid}")
			  ->one(Yii::$app->get("dogdb"));
	    return $result["num"];
	}

	/**
	 * [getForums description]
	 * @return [type] [description]
	 */
	public function getForums(){
		$query = new Query;
		$result = $query->select("fid,name")->from("pre_forum_forum")->where("status = 1 and fid not in(76,77,78,2)")->all(Yii::$app->get("dogdb"));
		return $result;
	}

	/**
     * 重设DB
     * @return null|object
     */
    private static function getDb(){
        return Yii::$app->get('dogdb');
    }
}