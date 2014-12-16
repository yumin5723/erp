<?php
namespace gcommon\cms\models;
use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;
use gcommon\cms\components\CmsActiveRecord;
use gcommon\cms\components\Publisher;
use gcommon\cms\components\ConstantDefine;
/**
 * This is the model class for table "{{object_term}}".
 *
 * The followings are the available columns in table '{{object_term}}':
 * @property string $object_id
 * @property string $term_id
 * @property integer $term_order
 */
class PageTerm extends CmsActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return ObjectTerm the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'page_term';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
	         'page'=>array(self::BELONGS_TO, 'Page',
	                'page_id'),
	         'term'=>array(self::BELONGS_TO, 'Oterm',
	                'term_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('page_id',$this->object_id,true);
		$criteria->compare('term_id',$this->term_id,true);
		$criteria->compare('data',$this->data,true);
		

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	/**
	 * save terms for object
	 * @param  intval $page_id 
	 * @param  array $terms 
	 * @return boolean
	 */
	public function saveActivePageTerm($page_id,$terms){
		foreach ( $terms as $term ) {
			if (!self::model()->findByPk(array("page_id"=>$page_id,"term_id"=>$term))){
				$obj_term=new self;
	            $obj_term->page_id=$page_id;
	            $obj_term->type= "active";
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
	public function updatePageTerm($page_id,$terms){
		// get current dependence
        $current = $this->getTermsByPage($page_id);
        // calculate need to delete
        $to_del = array_diff($current, $terms);
        // calculate need to insert
        $to_insert = array_diff($terms, $current);

        // save to db
        if (!empty($to_del)) {
            $this->removeDeps($page_id,$to_del);
        }
        if (!empty($to_insert)) {
            $this->addDeps($page_id,$to_insert);
        }
        return true;
	}
	/**
	 * get all acitivity pages
	 */
	public function getActivityPages(){
		$criteria = new CDbCriteria;
        return new CActiveDataProvider('PageTerm', array(
                'criteria' => array(
						"order"=>"page_id DESC",
						'with'=>array(
							'term'=>array(
									'condition'=>"level != 1",
								),
							),
						'together'=>true,
                	),
                'pagination'=>array(
                              'pageSize'=>20,
                          ),
            ) );
	}
	/**
	 * get belongs to active page ids
	 */
	public function getIdsBelongsToActivity(){
		$results = self::model()->findAll();
		$ids = array();
		foreach($results as $result){
			$ids[] = $result->page_id;
		}
		return array_unique($ids);
	}
	/**
     * get terms of page id 
     *
     * @param $page_id:
     *
     * @return
     */
    protected function getTermsByPage($page_id) {
        $rows = $this->getDbConnection()->createCommand()
                     ->select('term_id')
                     ->from($this->tableName())
                     ->where(array('and',
                             'page_id=:page_id',
                         ),
                         array(
                             ':page_id'=>$page_id,
                         ))
                     ->queryAll();
        return array_map(function($a){return $a['term_id'];},$rows);
    }
    /**
     * function_description
     *
     *
     * @return
     */
    protected function addDeps($model_id,$termids) {
        if (empty($termids)) {
            return true;
        }
        $sql = "INSERT INTO " . $this->tableName() . " (page_id,term_id) VALUES (:page_id,:term_id)";
        $cmd = $this->getDbConnection()->createCommand($sql);
        if (!is_array($termids)) {
            $termids = array($termids);
        }
        $cmd->bindParam(":page_id", $model_id);
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
    protected function removeDeps($model_id,$termids) {
        if (empty($termids)) {
            return true;
        }
        $sql="DELETE FROM " . $this->tableName() .
        " WHERE page_id=:page_id ";
        $sql .= " AND term_id in ('".implode("','",$termids)."')";
        if (!is_array($termids)) {
            $termids = array($termids);
        }
        $cmd = $this->getDbConnection()->createCommand($sql);
        $cmd->bindParam(":page_id", $model_id);
        return $cmd->execute();
    }
    /**
     * sign page type for admin menu like subject or special
     * @param  [type] $page_id [description]
     * @param  [type] $type    [description]
     * @param  [type] $term_id [description]
     * @return [type]          [description]
     */
    public function signPageType($page_id,$type,$term_id=null){
    	$model = new self;
    	$model->page_id = $page_id;
    	if(isset($term_id)){
    		$model->term_id = $term_id;
    	}
    	$model->type = $type;
    	$model->save(false);
    }
    /**
     * update page type
     * @param  [type] $page_id [description]
     * @param  [type] $type    [description]
     * @return [type]          [description]
     */
    public function updatePageType($page_id,$type){
    	$page = self::model()->findByAttributes(array("page_id"=>$page_id));
    	if(!empty($page)){
    		if($type == ConstantDefine::PAGE_TYPE_NONE){
    			self::model()->deleteAll("page_id = ".$page_id);
    		}else{
    			$page->type = $type;
    			$page->save(false);
    		}
    	}else{
    		$this->signPageType($page_id,$type);
    	}
    }
    public function getTypeByPageId($page_id){
    	$page = self::model()->findByAttributes(array("page_id"=>$page_id));
    	if(empty($page)){
    		return "none";
    	}
    	return $page->type;
    }
}