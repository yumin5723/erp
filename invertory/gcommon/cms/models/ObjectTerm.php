<?php
namespace gcommon\cms\models;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use gcommon\cms\components\CmsActiveRecord;
use gcommon\cms\components\Publisher;
use gcommon\cms\components\ConstantDefine;
use gcommon\cms\components\UploadFile;
use gcommon\cms\models\Oterm;
use yii\helpers\BaseArrayHelper;
/**
 * This is the model class for table "{{object_term}}".
 *
 * The followings are the available columns in table '{{object_term}}':
 * @property string $object_id
 * @property string $term_id
 * @property integer $term_order
 */
class ObjectTerm extends CmsActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'object_term';
    }
    /**
     * save terms for object
     * @param  intval $object_id
     * @param  array $terms
     * @return boolean
     */
    public function saveObjectTerm1($object_id,$terms){
        $oterm = new Oterm;
        $ids = array_unique(array_reduce($terms, function($r, $t) {
            foreach ($oterm->getAncestorsIdsByTerm($t) as $id) {
                $r[] = $id;
            }
            return $r;
         }, $terms));
        foreach ( $ids as $id ) {
            if (!self::findOne(["object_id"=>$object_id,"term_id"=>$id])){
                $obj_term=new ObjectTerm();
                $obj_term->object_id=$object_id;
                $obj_term->term_id=$id;
                $obj_term->save(false);
            }
        }
    }
    /**
     * update terms for object
     * @param  intval $object_id
     * @param  array $terms
     * @return boolean
     */
    public function updateObjectTerm1($object_id,$terms){
        ObjectTerm::model()->deleteAll('object_id = :id',array(':id'=>$object_id));
        $this->saveObjectTerm($object_id,$terms);
        return true;
    }
    /**
     * get all object ids by Term id include it's child
     * @param  $term_id
     * @return array object ids
     */
    public function getObjectIdsByTermId($term_id){
        $oterm = new Oterm;
        $ids = $oterm->getChildTerm($term_id);
        array_push($ids, $term_id);
        // $criteria = new CDbCriteria;
        // $criteria->addInCondition("term_id",$ids);

        $object_terms = self::find()->where(['term_id'=>$ids])->all();
        $oids = array();
        foreach($object_terms as $term){
            $oids[] = $term->object_id;
        }
        $nids = array_unique($oids);
        return $nids;
    }
    /**
     * fetch all objects by termid include its child term id
     * @param  [type] $term_id [description]
     * @return [type]          [description]
     */
    public function fetchObjectsByTermid($term_id){
        $objectIds = $this->getObjectIdsByTermId($term_id);
        // $criteria = new CDbCriteria;
        // $criteria->order = "object_id DESC";
        // $criteria->addInCondition("object_id",$objectIds);

        $query = Object::find()->where(['object_id'=>$objectIds])->orderBy(['object_id'=>SORT_DESC]);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
        
    }
    /**
     * fetch all objects by termid include its child term id
     * @param  [type] $term_id [description]
     * @return [type]          [description]
     */
    public function fetchObjectsByTermid1($term_id){

        $oterm = new Oterm;
        $ids = $oterm->getChildTerm($term_id);
        array_push($ids, $term_id);


        $query = self::find()->where(['term_id'=>$ids]);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
        
    }

    /**
     * function_description
     *
     * @param $object_id:
     *
     * @return
     */
    public function getAllTermsRefObject($object_id) {
        return array_map(function($term){return $term->term_id;},
            $this->findAllByAttributes(array('object_id'=>intval($object_id))));
    }
    /**
     * save terms for object
     * @param  intval $page_id 
     * @param  array $terms 
     * @return boolean
     */
    public function saveObjectTerm($object_id,$terms){
        foreach ( $terms as $term ) {
            if (!self::findOne(["object_id"=>$object_id,"term_id"=>$term])){
                $obj_term=new self;
                $obj_term->object_id=$object_id;
                $obj_term->term_id=$term;
                $obj_term->save(false);
            }
        }
    }
    /**
     * update terms for object
     * @param  intval $object_id 
     * @param  array $terms 
     * @return boolean
     */
    public function updateObjectTerm($object_id,$terms){

        
        // get current dependence
        $current = $this->getTermsByObject($object_id);
        // calculate need to delete
        $to_del = array_diff($current, $terms);
        // calculate need to insert
        $to_insert = array_diff($terms, $current);

        // save to db
        if (!empty($to_del)) {
            $this->removeTerms($object_id,$to_del);
        }
        if (!empty($to_insert)) {
            $this->addTerms($object_id,$to_insert);
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
    protected function getTermsByObject($object_id) {
        $query = new Query;
        $rows = $query->select('term_id')
                     ->from($this->tableName())
                     ->where(array('and',
                             'object_id=:object_id',
                         ),
                         array(
                             ':object_id'=>$object_id,
                         ))
                     ->all(static::getDb());
        return array_map(function($a){return $a['term_id'];},$rows);
    }
    /**
     * function_description
     *
     *
     * @return
     */
    protected function addTerms($model_id,$termids) {
        if (empty($termids)) {
            return true;
        }
        $sql = "INSERT INTO " . $this->tableName() . " (object_id,term_id) VALUES (:object_id,:term_id)";
        $cmd = static::getDb()->createCommand($sql);
        if (!is_array($termids)) {
            $termids = array($termids);
        }
        $cmd->bindParam(":object_id", $model_id);
        foreach ($termids as $id) {
            if(empty($id)){
                continue;
            }
            $cmd->bindParam(":term_id", $id);
            $cmd->execute();
        }
        return true;
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
    protected function removeTerms($model_id,$termids) {
        if (empty($termids)) {
            return true;
        }
        $sql="DELETE FROM " . $this->tableName() .
        " WHERE object_id=:object_id ";
        $sql .= " AND term_id in ('".implode("','",$termids)."')";
        if (!is_array($termids)) {
            $termids = array($termids);
        }
        $cmd = static::getDb()->createCommand($sql);
        $cmd->bindParam(":object_id", $model_id);
        return $cmd->execute();
    }
    /**
     * get ancestors ids by object id
     * @param  [type] $term_id [description]
     * @return array ids
     */
    public function getAncestorsIdsByObject($object_id){
        $results = self::find()->where(['object_id'=>$object_id])->orderby("term_id DESC")->all();
        if(empty($results)){
            return array();
        }
        $newArray = array();
        foreach($results as $result){
            $category= Oterm::findOne($result->term_id);
            $descendants=$category->ancestors()->all();
            $ret =  array_map(function ($a){return $a->id;}, $descendants);
            array_push($ret,$result->term_id);
            $newArray = array_merge($newArray,$ret);

        }
        $newArray = array_unique($newArray);
        // $category= Oterm::model()->findByPk($result->term_id);
        // if(empty($category)){
        //     return array();
        // }
        // $descendants=$category->ancestors()->findAll();
        // $ret =  array_map(function ($a){return $a->id;}, $descendants);
        // array_push($ret,$result->term_id);
        return $newArray;
    }

    public function getObjects(){
        return $this->hasOne(Object::className(),['object_id'=>'object_id']);
    }
    public function getTermname(){
        return $this->hasOne(Oterm::className(),['id'=>'term_id']);
    }
}