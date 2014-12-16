<?php
namespace gcommon\cms\models;
use gcommon\cms\components\CmsActiveRecord;
use yii\db\Query;
/**
 * This is the model class for table "{{tag_relationships}}".
 *
 * The followings are the available columns in table '{{tag_relationships}}':
 * @property string $tag_id
 * @property string $object_id
 */
class TagRelationships extends CmsActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'tag_relationships';
	}
    /**
     * update terms for object
     * @param  intval $object_id 
     * @param  array $terms 
     * @return boolean
     */
    public function updateObjectTags($object_id,$tags){
        // get current dependence
        $current = $this->getTagsRelationByObject($object_id);
        // calculate need to delete
        $to_del = array_diff($current, $tags);
        // calculate need to insert
        $to_insert = array_diff($tags, $current);
        // save to db
        if (!empty($to_del)) {
            $this->removeTag($object_id,$to_del);
        }
        if (!empty($to_insert)) {
            $this->addTag($object_id,$to_insert);
        }
        return true;
    }
    /**
     * function_description
     *
     *
     * @return
     */
    protected function addTag($model_id,$tagids) {
        if (empty($tagids)) {
            return true;
        }
        $sql = "INSERT INTO " . self::tableName() . " (object_id,tag_id) VALUES (:object_id,:tag_id)";
        $cmd = static::getDb()->createCommand($sql);
        if (!is_array($tagids)) {
            $tagids = array($tagids);
        }
        $cmd->bindParam(":object_id", $model_id);
        foreach ($tagids as $id) {
            if(empty($id)){
                continue;
            }
            $cmd->bindParam(":tag_id", $id);
            $cmd->execute();
        }
        return true;
    }
	/**
     * get terms of page id 
     *
     * @param $page_id:
     *
     * @return
     */
    public function getTagsRelationByObject($object_id) {
        $query = new Query;
        $rows = $query->select('tag_id')
                     ->from(self::tableName())
                     ->where(array('and',
                             'object_id=:object_id',
                         ),
                         array(
                             ':object_id'=>$object_id,
                         ))
                     ->all(static::getDb());
        return array_map(function($a){return $a['tag_id'];},$rows);
    }
    /**
     * function_description
     *
     * @param $model_type:
     * @param $model_id:
     * @param $dep_type:
     * @param $dep_ids:
     *
     * @return
     */
    protected function removeTag($model_id,$tagids) {
        if (empty($tagids)) {
            return true;
        }
        $sql="DELETE FROM " . self::tableName() .
        " WHERE object_id=:object_id ";
        $sql .= " AND tag_id in ('".implode("','",$tagids)."')";
        if (!is_array($tagids)) {
            $tagids = array($tagids);
        }
        $cmd = static::getDb()->createCommand($sql);
        $cmd->bindParam(":object_id", $model_id);
        return $cmd->execute();
    }
}