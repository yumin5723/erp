<?php
namespace gcommon\cms\models;
use Yii;
use yii\db\ActiveRecord;
use gcommon\cms\components\CmsActiveRecord;
use yii\helpers\BaseArrayHelper;
use gcommon\cms\components\widgets\CmsWidgetFactory;
use gcommon\cms\components\ConstantDefine;
class Block extends CmsActiveRecord {
    protected $_widget = null;
    public $category_id;
    public $count;

    /**
     * function_description
     *
     *
     * @return
     */
    public static function tableName() {
        return 'block';
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function rules() {
        return [
            [['name','type','page_type'],'required'],
            [['content','params'],'safe'],            
        ];
    }

    /**
     * function_description
     *
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
        );
    }
    public function behaviors()
    {
        return BaseArrayHelper::merge(
            parent::behaviors(),
            [
               'CmsEventBehavior' => [
                    'class' => 'gcommon\cms\components\CmsEventBehavior',
               ],
               'cms_type' => [
                    'class' => 'gcommon\cms\components\CmsTypeBehavior',
                ],
                'cms_event' => [
                    'class' => 'gcommon\cms\components\CmsEventBehavior',
                ],
                'cms_dependence' => [
                    'class' => 'gcommon\cms\components\CmsDepBehavior',
                ],
              'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created', 'modified'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'modified',
                ],
                'value' => function (){ return date("Y-m-d H:i:s");}
            ],
           ]
        );
    }
    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    // public function search() {
    //     // Warning: Please modify the following code to remove attributes that
    //     // should not be searched.

    //     $criteria=new CDbCriteria;
    //     $criteria->compare( 'id', $this->id, true );
    //     $criteria->compare( 'name', $this->name, true );
    //     $criteria->compare( 'type', $this->type, true );

    //     $sort = new CSort;
    //     $sort->attributes = array(
    //         'id',
    //     );
    //     $sort->defaultOrder = 'id DESC';


    //     return new CActiveDataProvider( $this, array(
    //             'criteria'=>$criteria,
    //             'pagination' => array(
    //                 'pageSize' => 20,
    //             ),
    //             'sort'=>$sort
    //         ) );
    // }
   /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'page_type'=>'所属页面',
            'name' => '名字',
            'type'=>'类型',
            'params[category_id]' => '分类ID',
            'params[count]'=> '需要的条数',
            'html'=>'最终代码',
            'content'=>'内容'
        );
    }
    
    /**
     * Convert from value to the String of the Block Type
     *
     * @param type    $value
     */
    public static function convertBlockType( ) {
        return '
            $types = gcommon\cms\components\ConstantDefine::getBlockType();
            if ( isset( $types[$model->type] ) ) {
                return $types[$model->type];
            } else {
                return "undefined";
            }
        ';
    }
    public function getBuildLink(){
        return '
            if($model->type!=5){
                return \yii\helpers\Html::a("生成内容","/cms/block/build?id=$model->id",["target"=>"_blank"]);
            }else{
                return "无需发布";
            }
        ';
    }
    public function getBlockStatus(){
        return '
            if($model->html!=""){
                return "已生成内容";
            }else{
                return "还未生成内容";
            }
        ';
    }
    /**
     * save in table when update block
     */
    public function backupBlock(){
        $model = new BlockBackup;
        $model->content = $this->content;
        $model->block_id = $this->id;
        $model->created_id = $this->modified_id;
        return $model->save(false);
    }

    /**
     * get block widget
     *
     * @return
     */
    public function getWidget() {
        if (is_null($this->_widget)) {
            // factory _widget
            if (!is_array($this->params)) {
                @$params = unserialize($this->params);
            }else{
                $params = $this->params;
            }
            if (!$params) {
                throw new CException("Can not read params from Block, ".$this->id);
            }
            // add content to params
            $params['block_content'] = $this->content;
            $params['block_id'] = $this->id;
            $this->_widget = CmsWidgetFactory::factory($this->type, $params);

        }
        return $this->_widget;
    }

    /**
     * function_description
     *
     *
     * @return
     */
    protected function updateDependentCategory() {
        $category_ids = $this->getWidget()->getDependentCategoryIds();
        $this->UpdateDependentCategoryByIds($category_ids);
    }


    /**
     * function_description
     *
     *
     * @return
     */
    public function beforeSave($insert)
    {
        // process dependent category

        if (is_array($this->params)) {
            $this->params = serialize($this->params);
        }
        return parent::beforeSave($insert);
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function afterSave($insert,$changedAttributes) {
        $this->updateDependentCategory();

        $this->fireUpdate();
        return parent::afterSave($insert,$changedAttributes);
    }



    /**
     * function_description
     *
     *
     * @return
     */
    public function afterFind() {
        if (!is_array($this->params)) {
            @$this->params = unserialize($this->params);
            // $ret = [];
            if(isset($this->params['picture'])){
                foreach($this->params['picture'] as $key=>$value){
                    if(!isset($value['order'])){
                        @$this->params['picture'][$key]['order'] = 0;
                    }
                }
            }
        }
    }

    /**
     * get block as html
     * current only return $this->render()
     * maybe add cache for render result someday
     *
     * @return
     */
    public function updateHtml() {
        $new_html = $this->render();
        if ($new_html != $this->html) {
            Yii::Info("it will update html for block_id .".$this->id);
            $this->html = $new_html;
            $this->update();
            $this->firePublished();
        }
        return true;
    }


    /**
     * block render self, return current html content
     *
     *
     * @return
     */
    public function render() {
        return $this->getWidget()->run();
    }

    /**
     * save block
     */
    public function saveBlock($uid){
        $this->created_id = $uid;
        $this->modified_id = $uid;
        $this->save(false);
        return true;
    }
    /**
     * save block
     */
    public function updateBlock($uid){
        $this->modified_id = $uid;
        $this->save(false);
        // $this->backupBlock();
        return true;
    }

    /**
     * get objects that the model dependent.
     *
     * @param $model_type:
     * @param $model_id:
     * @param $dep_type:
     *
     * @return
     */
    public function getDeps($page_type, $page_id, $dep_type) {
        $rows = $this->getDbConnection()->createCommand()
                     ->select('dep_id')
                     ->from('obj_dependence')
                     ->where(array('and',
                             'obj_type=:obj_type',
                             'dep_type=:dep_type',
                             'obj_id=:obj_id',
                         ),
                         array(
                             ':obj_type'=>$page_type,
                             ':obj_id'=>intval($page_id),
                             ':dep_type'=>$dep_type
                         ))
                     ->queryAll();
        return array_map(function($a){return $a['dep_id'];},$rows);
    }

    public function getSpecialPagesBlock($page_type, $page_id, $dep_type){
        $blocks = $this->getDeps($page_type, $page_id, $dep_type);
        $criteria = new CDbCriteria;
        $criteria->addInCondition('id',$blocks);
        return self::model()->findAll($criteria);
    }
    /**
     * [getBlockPageType description]
     * @return [type] [description]
     */
    public function getBlockPageType(){
        $pageTypes = ConstantDefine::getBlockPageType();
        return $pageTypes;
    }
}