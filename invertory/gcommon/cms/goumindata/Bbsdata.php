<?php
namespace gcommon\cms\goumindata;
use Yii;
use yii\db\Query;
use gcommon\cms\components\GData;
class Bbsdata extends GData{
	const FOCUS_BLOCK_ID = 96;
	const NEWS_HEADLINE_BLOCK_ID = 97;
	const NEWS_LIST_BLOCK_ID = 99;
	const HOT_THREADS_BLOCK_ID = 119;
	const REPLY_NEW_BLOCK_ID = 124;
	const NEW_THREAD_BLOCK_ID = 125;
	const BBS_GREAT_PERSON_BLOCK_ID = 165;
	/**
	 * [getBbsFocus description]
	 * @return [type] [description]
	 */
	public static function getBbsFocus(){
		$query = new Query;
		$results = $query
				->select('url,title,pic')
				->from(self::getBlockTableName())
				->where(['bid'=>self::FOCUS_BLOCK_ID])
				->all(self::getDb());
		return $results;
	}
	/**
	 * find from name
	 * @return [type] [description]
	 */
	public static function getBlockTableName(){
		return 'pre_common_block_item';
	}
	/**
	 * [getBbsNewsHeadline description]
	 * @return [type] [description]
	 */
	public static function getBbsNewsHeadline(){
		$query = new Query;
		$results = $query
				->select('url,title,summary')
				->from(self::getBlockTableName())
				->where(['bid'=>self::NEWS_HEADLINE_BLOCK_ID])
				->all(self::getDb());
		return $results;
	}
	/**
	 * [getBbsNewsList description]
	 * @return [type] [description]
	 */
	public static function getNewsList(){
		$query = new Query;
		$results = $query
				->select('url,title,fields')
				->from(self::getBlockTableName())
				->where(['bid'=>self::NEWS_LIST_BLOCK_ID])
				->all(self::getDb());
		$ret = [];
		foreach($results as $key=>$result){
			$ret[$key]['url'] = $result['url'];
			$ret[$key]['title'] = $result['title'];
			$fields = unserialize($result['fields']);
			$ret[$key]['forumname'] = $fields['forumname'];
		}
		return $ret;
	}
	/**
	 * [getHotThreadsList description]
	 * @return [type] [description]
	 */
	public static function getHotThreadsList(){
		return self::getThreadsListByBlockId(self::HOT_THREADS_BLOCK_ID);
	}
	/**
	 * [getHotThreadsList description]
	 * @return [type] [description]
	 */
	public static function getReplyNewList(){
		return self::getThreadsListByBlockId(self::REPLY_NEW_BLOCK_ID);
	}
	/**
	 * [getHotThreadsList description]
	 * @return [type] [description]
	 */
	public static function getNewThreadList(){
		return self::getThreadsListByBlockId(self::NEW_THREAD_BLOCK_ID);
	}
	/**
	 * [getThreadsListByBlockId description]
	 * @param  [type] $block_id [description]
	 * @return [type]           [description]
	 */
	public static function getThreadsListByBlockId($block_id){
		$query = new Query;
		$results = $query
				->select('url,title')
				->from(self::getBlockTableName())
				->where(['bid'=>$block_id])
				->all(self::getDb());
		return $results;
	}
	/**
	 * [getGreadPerson description]
	 * @return [type] [description]
	 */
	public static function getGreadPerson(){
		$query = new Query;
		$results = $query
				->select('uid,title')
				->from(self::getBlockTableName())
				->where(['bid'=>self::BBS_GREAT_PERSON_BLOCK_ID])
				->all(self::getDb());
		return $results;
	}

}