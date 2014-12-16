<?php
namespace gcommon\cms\models;
use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;
use gcommon\cms\components\CmsActiveRecord;
use gcommon\cms\components\Publisher;
use gcommon\cms\components\ConstantDefine;
use gcommon\cms\components\UploadFile;
use gcommon\cms\models\Template;
use yii\helpers\BaseArrayHelper;
use yii\web\HttpException;

class Object extends CmsActiveRecord {
    /**
     * object arrribute is hot
     */
    const IS_HOT = 1;
    public $term_id;
    public $template_id;
    /**
     *
     *
     * @return string the associated database table name
     */
    public static function tableName() {

        return 'object';
    }
    /**
     *
     *
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            // ['total_number_meta', 'total_number_resource', 'object_slug','safe'],
            [['object_name','object_list_name',],'required'],
            [['object_author_name','tags','object_content','source','object_excerpt','object_date','object_title','object_keywords','object_description','template_id'],'safe'],
            // ['object_content','length','min' => 10],
            // ['object_description,object_keywords,object_excerpt,object_title,guid','safe'],
            // ['object_status', 'comment_status', 'lang', 'total_number_meta', 'total_number_resource', 'object_view', 'like', 'dislike', 'rating_scores','numerical','integerOnly' => true],
            // ['rating_average','numerical'
            // ],
            // // ['object_author', 'object_password', 'object_parent', 'object_type', 'comment_count','length','max' => 20],
            // ['guid', 'object_keywords', 'object_author_name','length','max' => 255],
            // ['layout','length','max' => 125],
            // ['tags','checkTags'],
            // ['person','ishot','istop','isred','url','safe'],
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            // ['object_id', 'object_author', 'object_date', 'object_content', 'object_title', 'object_status', 'object_name','safe','on' => 'search,draft,published,pending'
            // ],
        ];
    }
    /**
     *
     *
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {

        return self::extraLabel();
    }
    public function behaviors() {
        return BaseArrayHelper::merge(
            parent::behaviors(),
            [
               'CmsEventBehavior' => [
                    'class' => 'gcommon\cms\components\CmsEventBehavior',
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
    public function beforeSave($insert) {
        if ( parent::beforeSave($insert) ) {
            if ( $this->isNewRecord ) {
                if ( $this->object_type == '' ) $this->object_type = 'object';
                self::extraBeforeSave( 'create', $this );
            } else {
                self::extraBeforeSave( 'update', $this );
            }
            return true;
        } else
            return false;
    }
    public function afterSave($insert,$changedAttributes){
        // if(parent::afterSave($insert)){
            $tags = @explode(',', $this->tags);
            $ret = [];
            foreach($tags as $tag){
                $result = Tag::find()->where(['name'=>$tag])->one();
                if(empty($result)){
                    $tagModel = new Tag;
                    $tagModel->name = $tag;
                    $tagModel->save(false);
                    array_push($ret, $tagModel->id);
                }else{
                    array_push($ret, $result->id);
                }
            }
            $tagRelation = new TagRelationships;
            $tagRelation->updateObjectTags($this->id,$ret);
        // }
    }
    public static function extraBeforeSave( $type = 'update', $object ) {
        if (!isset(Yii::$app->user)) {
            return;
        }
        switch ( $type ) {
        case 'update':
            $object->object_modified_uid = Yii::$app->user->id;
            break;

        case 'create':
            $object->object_author = Yii::$app->user->id;
            $object->object_modified_uid = Yii::$app->user->id;
            if ( $object->guid == '' ) {
                $object->guid = uniqid();
            }
            break;
        }
    }
    /**
     * Excute after Delete Object
     */
    public function afterDelete() {
        parent::afterDelete();
        self::extraAfterDelete( $this );
        //Implements to delete The Term Relation Ship

    }
    /**
     * Update Tag Relationship of the Object
     *
     * @param type    $obj
     */
    public static function UpdateTagRelationship( $obj ) {
        Tag::model()->updateFrequency( $obj->_oldTags, $obj->tags );
        //Start to DElete All the Tag Relationship
        TagRelationships::model()->deleteAll( 'object_id = :id', array(
                ':id' => $obj->object_id
            ) );
        //Start to re Insert
        $explode = explode( ',', trim( $obj->tags ) );

        foreach ( $explode as $ex ) {
            $tag = Tag::model()->find( 'slug = :s', array(
                    ':s' => Tag::model()->stripVietnamese( strtolower( $ex ) )
                ) );
            if ( $tag ) {
                $tag_relationship = new TagRelationships;
                $tag_relationship->tag_id = $tag->id;
                $tag_relationship->object_id = $obj->object_id;
                $tag_relationship->save();
            }
        }
    }
    /**
     * get Related content by Tags
     *
     * @param type    $id
     * @param type    $max
     * @return CActiveDataProvider
     */
    public static function getRelatedContentByTags( $id, $max ) {
        $object = Object::loadModel( $id );
        $criteria = new CDbCriteria;
        $criteria->join = 'join tag_relationships ft on ft.object_id = t.object_id';
        $criteria->condition = 'ft.tag_id in (select tag_id from tag_relationships fr
                                                where fr.object_id = :id)
                                    AND t.object_id <> :id
                                    AND t.object_status = :status
                                    AND t.object_date <= :time
                                    AND t.object_type = :type';
        $criteria->distinct = true;
        $criteria->params = array(
            ':id' => $id,
            ':status' => ConstantDefine::OBJECT_STATUS_PUBLISHED,
            ':time' => time() ,
            'type' => $object->object_type
        );
        $criteria->order = "object_date DESC";
        //$aa = Object::model()->findAll($criteria);
        //$criteria->limit = $max;

        return new CActiveDataProvider( 'Object', array(
                'criteria' => $criteria,
                'pagination' => array(
                    'pageSize' => $max
                )
            ) );
    }
    /**
     * Normalize The Tags for the Object - Check Valid
     *
     * @param type    $attribute
     * @param type    $params
     */
    public function normalizeTags( $attribute, $params ) {
        $this->tags = Tag::array2string( array_unique( Tag::string2array( $this->tags ) ) );
    }
    /**
     * Check Tags Valid
     *
     * @param type    $attribute
     * @param type    $params
     */
    public function checkTags( $attribute, $params ) {
        $result = $this->tags;
        $regex = "/[\^\[\]\$\.\|\?\*\+\(\)\{\}\/\*\%\!\.\'\"\@\#\&\:\<\>\|\-\_\+\=\`\~\;]/";
        if ( preg_match( $regex, $result ) ) $this->addError( 'tags', Yii::t( 'Tags must contain characters only' ) );
    }
    /**
     * get content status
     *
     * @return array
     */
    public function getContentStatus() {
        $content_status = array(
            "1"=>"草稿",
            "2"=>"待发布",
            "3"=>"已发布",
        );
        return $content_status;
    }
    public static function extraSearch( $object ) {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.
        $criteria = new CDbCriteria;
        $criteria->compare( 'object_id', $object->object_id, true );
        $criteria->compare( 'object_author', $object->object_author, true );
        $criteria->compare( 'object_date', $object->object_date );
        $criteria->compare( 'object_content', $object->object_content, true );
        $criteria->compare( 'object_title', $object->object_title, true );
        $criteria->compare( 'object_status', $object->object_status );
        $criteria->compare( 'ishot', $object->ishot );
        $criteria->compare( 'istop', $object->istop );
        $criteria->compare( 'isred', $object->isred );
        $sort = new CSort;
        $sort->attributes = array(
            'object_id',
        );
        $sort->defaultOrder = 'object_id DESC';

        return new CActiveDataProvider( $object, array(
                'criteria' => $criteria,
                'pagination' => array(
                    'pageSize' => 20,
                ),
                'sort' => $sort
            ) );
    }
    public static function extraLabel() {

        return [
            'object_id' => '文章id',
            'object_author' => '文章作者',
            'template_id'=> '模板',
            'object_content'=>'内容',
            'object_date' => '发布时间',
            'object_date_gmt' => 'Object Date Gmt',
            'object_title' => 'SEO TITLE',
            'ishot' => '是否热点',
            'object_excerpt' => '文章简介',
            'object_status' => '状态',
            'comment_status' => 'Comment Status',
            'object_password' => 'Object Password',
            'object_name' => '文章标题',
            'object_list_name' => '列表标题',
            'object_modified' => 'Object Modified',
            'object_modified_gmt' => 'Object Modified Gmt',
            'object_content_filtered' => 'Object Content Filtered',
            'object_parent' => 'Object Parent',
            'guid' => 'Guid',
            'object_type' => 'Object Type',
            'comment_count' => 'Comment Count',
            'object_slug' => 'Object Slug',
            'object_description' => 'SEO 介绍',
            'object_keywords' => 'SEO 关键字',
            'lang' => 'Language',
            'object_author_name' => '作者',
            'total_number_meta' => 'Total Number Meta',
            'total_number_resource' => 'Total Number Resource',
            'tags' => '标签',
            'object_view' => 'Object View',
            'like' => 'Like',
            'dislike' => 'Dislike',
            'rating_scores' => 'Rating Scores',
            'rating_average' => 'Rating Average',
            'layout' => 'Layout',
            'person' => 'Person' ,
            'source'=>'来源'
        ];
    }
    /**
     * Define Relationships so that its child class can call it
     *
     * @return type
     */
    public static function extraRelationships() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.

        return array(
            'author' => array(
                self::BELONGS_TO,
                'User',
                'object_author'
            ) ,
        );
    }
    /**
     * Load Object that has been published and time is <= time()
     *
     * @param type    $id
     * @return type
     */
    public static function loadPublishedModel( $id ) {
        $model = Object::model()->findByPk( (int)$id );
        if ( $model === null ) throw new CHttpException( 404, 'The requested page does not exist.' );
        else {
            if ( ( $model->object_status == ConstantDefine::OBJECT_STATUS_PUBLISHED ) && ( $model->object_date <= time() ) ) {

                return $model;
            } else {
                throw new CHttpException( 404, 'The requested page does not exist.' );
            }
        }
    }
    /**
     * Save Meta Data of a Object Content Type
     *
     * @param type    $key
     * @param type    $value
     * @param type    $object
     * @param type    $create
     */
    public static function saveMetaValue( $key, $value, $object, $create = true ) {
        if ( $create ) {
            $object_meta = new ObjectMeta;
            $object_meta->meta_key = $key;
            $object_meta->meta_value = $value;
            $object_meta->meta_object_id = $object->object_id;
            $object_meta->save();
        } else {
            $object_meta = ObjectMeta::model()->find( 'meta_key= :key  and meta_object_id = :obj ', array(
                    ':key' => $key,
                    ':obj' => $object->object_id
                ) );
            if ( $object_meta != null ) {
                $object_meta->meta_value = $value;
                $object_meta->save();
            } else {
                $object_meta = new ObjectMeta;
                $object_meta->meta_key = $key;
                $object_meta->meta_value = $value;
                $object_meta->meta_object_id = $object->object_id;
                $object_meta->save();
            }
        }
    }
    /**
     * Convert from value to the String of the Object Status
     *
     * @param type    $value
     */
    public static function convertObjectStatus() {

        return '
            $status = gcommon\cms\components\ConstantDefine::getObjectStatus();
            if ( isset( $status[$model->objects->object_status] ) ) {
                return $status[$model->objects->object_status];
            } else {
                return "undefined";
            }
        ';
    }
    /**
     * Convert from value to the String of the Object Status
     *
     * @param type    $value
     */
    public static function convertObjectStatus1() {

        return '
            $status = gcommon\cms\components\ConstantDefine::getObjectStatus();
            if ( isset( $status[$model->object_status] ) ) {
                return $status[$model->object_status];
            } else {
                return "undefined";
            }
        ';
    }
    public function getPreviewLink(){
        return '
            return \yii\helpers\Html::a("预览","/cms/object/preview?id=$model->object_id",["target"=>"_blank"]);
        ';
    }
    public function getUpdateLink(){
        return '
            return \yii\helpers\Html::a("修改","/cms/object/update?id=$model->object_id",["target"=>"_blank"]);
        ';
    }
    public function getOperationLink(){
      return '
          if($model->object_status == gcommon\cms\components\ConstantDefine::OBJECT_STATUS_DRAFT){
             return \yii\helpers\Html::a("发布","/cms/object/publish?id=$model->object_id");
          }elseif($model->object_status == gcommon\cms\components\ConstantDefine::OBJECT_STATUS_PUBLISHED){
             return \yii\helpers\Html::a("重新发布","/cms/object/publish?id=$model->object_id");
          }else{
             return "";
          }
      ';
    }
    public function getOperationLink1(){
      return '
          if($model->objects->object_status == gcommon\cms\components\ConstantDefine::OBJECT_STATUS_DRAFT){
             return \yii\helpers\Html::a("发布","/cms/object/publish?id=$model->object_id");
          }elseif($model->objects->object_status == gcommon\cms\components\ConstantDefine::OBJECT_STATUS_PUBLISHED){
             return \yii\helpers\Html::a("重新发布","/cms/object/publish?id=$model->object_id");
          }else{
             return "";
          }
      ';
    }
    /**
     * Convert from value to the String of the Object url
     *
     * @param type    $value
     */
    public static function convertObjectUrl( $value ) {
        if(empty($value)){
            return "";
        }
        return $domain = Yii::app()->getModule("cms")->domain.$value;
    }
    /**
     * Convert from value to the String of the Object Comment
     *
     * @param type    $value
     */
    public static function convertObjectCommentType( $value ) {
        $types = ConstantDefine::getObjectCommentStatus();
        if ( isset( $types[$value] ) ) {

            return $types[$value];
        } else {

            return t( 'cms', 'undefined' );
        }
    }
    /**
     * Get the history workflow of the Object
     *
     * @param type    $object
     */
    public static function getTransferHistory( $model ) {
        $trans = Transfer::model()->with( 'from_user' )->findAll( array(
                'condition' => ' object_id=:obj ',
                'params' => array(
                    ':obj' => $model->object_id
                ) ,
                'order' => 'transfer_id ASC'
            ) );
        $trans_list = "<ul>";
        $trans_list.= "<li>- <b>" . $model->author->display_name . "</b> " . t( "cms", "created on" ) . " <b>" . date( 'm/d/Y H:i:s', $model->object_modified ) . "</b></li>";
        //Start to Translate all the Transition

        foreach ( $trans as $tr ) {
            if ( $tr->type == ConstantDefine::TRANS_STATUS ) {
                $temp = "<li>- <b>" . $tr->from_user->display_name . "</b> " . t( "cms", "changed status to" ) . " <b>" . self::convertObjectStatus( $tr->after_status ) . "</b> " . t( "cms", "on" ) . " <b>" . date( 'm/d/Y H:i:s', $tr->time ) . "</b></li>";
            }
            if ( $tr->type == ConstantDefine::TRANS_ROLE ) {
                $temp = "<li>- <b>" . $tr->from_user->display_name . "</b> " . t( "cms", "modified and sent to" ) . " <b>" . ucfirst( $tr->note ) . "</b> " . t( "cms", "on" ) . " <b>" . date( 'm/d/Y H:i:s', $tr->time ) . "</b></li>";
            }
            if ( $tr->type == ConstantDefine::TRANS_PERSON ) {
                $to_user = User::model()->findbyPk( $tr->to_user_id );
                $name = "";
                if ( $to_user != null ) $name = $to_user->display_name;
                $temp = "<li>- <b>" . $tr->from_user->display_name . "</b> " . t( "cms", "modified and sent to" ) . " <b>" . ucfirst( $name ) . "</b> " . t( "cms", "on" ) . " <b>" . date( 'm/d/Y H:i:s', $tr->time ) . "</b></li>";
            }
            $trans_list.= $temp;
        }
        $trans_list.= '</ul>';

        return $trans_list;
    }
    /**
     * Convert from value to the String of the Object Type
     *
     * @param type    $value
     */
    public static function convertObjectType( $value ) {
        $types = GxcHelpers::getAvailableContentType();
        if ( isset( $types[$value]['name'] ) ) {

            return $types[$value]['name'];
        } else {

            return t( 'cms', 'undefined' );
        }
    }
    /**
     * Do Search Object based on its status
     *
     * @param type    $type
     * @return CActiveDataProvider
     */
    public function doSearch( $type = 0 ) {
        $criteria = new CDbCriteria;
        $sort = new CSort;
        $sort->attributes = array(
            'object_id',
        );
        $sort->defaultOrder = 'object_id DESC';

        switch ( $type ) {
            //If looking for DRAFT Content

        case ConstantDefine::OBJECT_STATUS_DRAFT:
            $criteria->condition = 'object_status = :status and object_author = :uid';
            $criteria->params = array(
                ':status' => ConstantDefine::OBJECT_STATUS_DRAFT,
                ':uid' => Yii::app()->user->id
            );
            break;

        case ConstantDefine::OBJECT_STATUS_PUBLISHED:
            //Do nothing;
            $criteria->condition = 'object_status = :status';
            $criteria->params = array(
                ':status' => ConstantDefine::OBJECT_STATUS_PUBLISHED
            );
            break;

        case self::OBJECT_TYPE_GAME:
            //Do nothing;
            $criteria->condition = 'object_type = :type';
            $criteria->params = array(
                ':type' => self::OBJECT_TYPE_GAME
            );
            break;
        case self::OBJECT_TYPE_ARTICLE:
            //Do nothing;
            $criteria->condition = 'object_type = :type';
            $criteria->params = array(
                ':type' => self::OBJECT_TYPE_ARTICLE
            );
            break;
        }
        $criteria->compare( 'object_id', $this->object_id, true );
        $criteria->compare( 'object_author', $this->object_author, true );
        $criteria->compare( 'object_date', $this->object_date );
        $criteria->compare( 'object_content', $this->object_content, true );
        $criteria->compare( 'object_title', $this->object_title, true );
        $criteria->compare( 'object_name', $this->object_name, true );
        $criteria->addCondition("object_status!=".ConstantDefine::OBJECT_STATUS_DELETE);

        return new CActiveDataProvider( get_class( $this ) , array(
                'criteria' => $criteria,
                'sort' => $sort,
                'pagination'=>array(
                              'pageSize'=>20,
                          ),
            ) );
    }
    public static function buildLink( $obj ) {
        if ( $obj->object_id )
            return FRONT_SITE_URL . "/article?id=" . $obj->object_id . "&slug=" . $obj->object_slug;
        else
            return null;
    }
    public function getObjectLink() {
        if ( $this->object_id ) {
            $class_name = GxcHelpers::getClassOfContent( $this->object_type );
            if ( $class_name != 'Object' ) {
                Yii::import( 'common.content_type.' . $this->object_type . '.' . $class_name );
            }

            return $class_name::buildLink( $this );
        } else {

            return null;
        }
    }
    public function suggestContent( $keyword, $type = '', $limit = 20 ) {
        if ( $type == '' ) {
            $objects = $this->findAll( array(
                    'condition' => 'object_name LIKE :keyword',
                    'order' => 'object_id DESC',
                    'limit' => $limit,
                    'params' => array(
                        ':keyword' => '%' . strtr( $keyword, array(
                                '%' => '\%',
                                '_' => '\_',
                                '\\' => '\\\\'
                            ) ) . '%',
                    ) ,
                ) );
        } else {
            $objects = $this->findAll( array(
                    'condition' => 'object_type = :t and object_name LIKE :keyword',
                    'order' => 'object_name DESC',
                    'limit' => $limit,
                    'params' => array(
                        ':t' => trim( strtolower( $type ) ) ,
                        ':keyword' => '%' . strtr( $keyword, array(
                                '%' => '\%',
                                '_' => '\_',
                                '\\' => '\\\\'
                            ) ) . '%',
                    ) ,
                ) );
        }
        $names = array();

        foreach ( $objects as $object ) $names[] = str_replace( ";", "", $object->object_name ) . "|" . $object->object_id;

        return $names;
    }
    public static function Resources() {

        return array(
            'thumbnail' => array(
                'type' => 'thumbnail',
                'name' => '缩略图',
                'maxSize' => "10485760",
                'minSize' => "1",
                'max' => 1,
                'allow' => array(
                    'jpeg',
                    'jpg',
                    'gif',
                    'png'
                )
            )
        );
    }
    public static function Permissions() {

        return array(
            'Admin' => array(
                'allowedObjectStatus' => array(
                    ConstantDefine::OBJECT_STATUS_DRAFT => array(
                        'condition' => ''
                    ) ,
                    ConstantDefine::OBJECT_STATUS_PENDING => array(
                        'condition' => ''
                    ) ,
                    ConstantDefine::OBJECT_STATUS_PUBLISHED => array(
                        'condition' => ''
                    ) ,
                    ConstantDefine::OBJECT_STATUS_HIDDEN => array(
                        'condition' => ''
                    ) ,
                ) ,
                'allowedTransferto' => array(
                    'Editor' => array(
                        'condition' => ''
                    ) ,
                    'Reporter' => array(
                        'condition' => ''
                    ) ,
                ) ,
                'allowedToCreateContent' => true,
                'allowedToUpdateContent' => ''
            ) ,
            'Editor' => array(
                'allowedObjectStatus' => array(
                    ConstantDefine::OBJECT_STATUS_DRAFT => array(
                        'condition' => 'return $params["new_content"]==true;'
                    ) ,
                    ConstantDefine::OBJECT_STATUS_PENDING => array(
                        'condition' => ''
                    ) ,
                    ConstantDefine::OBJECT_STATUS_PUBLISHED => array(
                        'condition' => ''
                    ) ,
                    ConstantDefine::OBJECT_STATUS_HIDDEN => array(
                        'condition' => 'return $params["new_content"]==false;'
                    ) ,
                ) ,
                'allowedTransferto' => array(
                    'Editor' => array(
                        'condition' => ''
                    ) ,
                    'Reporter' => array(
                        'condition' => ''
                    ) ,
                ) ,
                'allowedToCreateContent' => true,
                'allowedToUpdateContent' => '
                                        return (($params["new_content"]==false)&&
                                        (($params["content_status"]==ConstantDefine::OBJECT_STATUS_PUBLISHED)
                                        ||(($params["content_status"]==ConstantDefine::OBJECT_STATUS_DRAFT)&&($params["content_author"]==user()->id))
                                        ||(($params["content_status"]==ConstantDefine::OBJECT_STATUS_PENDING)&&($params["trans_to"]==user()->id))
                                        ||(($params["content_status"]==ConstantDefine::OBJECT_STATUS_PENDING)&&($params["trans_type"]==ConstantDefine::TRANS_ROLE)&&(array_key_exists($params["trans_note"],Rights::getAssignedRoles(user()->id,true))))
                                        ));'
            ) ,
            'Reporter' => array(
                'allowedObjectStatus' => array(
                    ConstantDefine::OBJECT_STATUS_DRAFT => array(
                        'condition' => 'return
                                                           ($params["new_content"]==true) ;
                                                           '
                    ) ,
                    ConstantDefine::OBJECT_STATUS_PENDING => array(
                        'condition' => 'return
                                                           ((($params["new_content"]==false)&&($params["content_status"]!=ConstantDefine::OBJECT_STATUS_PUBLISHED)&&(($params["trans_to"]==user()->id)||($params["trans_to"]==0)))||

                                                           ($params["new_content"]==true)) ;
                                                           '
                    ) ,
                    ConstantDefine::OBJECT_STATUS_HIDDEN => array(
                        'condition' => 'return
                                                          (($params["new_content"]==false)&&($params["content_status"]==ConstantDefine::OBJECT_STATUS_DRAFT)&&($params["content_author"]==user()->id)) ;
                                                          '
                    ) ,
                ) ,
                'allowedTransferto' => array(
                    'Editor' => array(
                        'condition' => ''
                    ) ,
                    'Reporter' => array(
                        'condition' => ''
                    ) ,
                ) ,
                'allowedToCreateContent' => true,
                'allowedToUpdateContent' => '
                                        return (($params["new_content"]==false)&&
                                        ((($params["content_status"]==ConstantDefine::OBJECT_STATUS_DRAFT)&&($params["content_author"]==user()->id))
                                        ||(($params["content_status"]==ConstantDefine::OBJECT_STATUS_PENDING)&&($params["trans_to"]==user()->id))
                                        ||(($params["content_status"]==ConstantDefine::OBJECT_STATUS_PENDING)&&($params["trans_type"]==ConstantDefine::TRANS_ROLE)&&(array_key_exists($params["trans_note"],Rights::getAssignedRoles(user()->id,true))))
                                        )) ;'
            )
        );
    }
    /**
     * Increase the comment count by 1 whenever new comment was created.
     */
    public function increaseCommentCount() {
        if ( $this->comment_count != null ) $this->comment_count++;
        else $this->comment_count = 1;
        $this->save();
    }
    public function getResource() {
        return CMap::mergeArray( self::Resources(),
            array(
                'video'=>array( 'type'=>'video',
                    'name'=>'视频',
                    'maxSize'=>"10485760",
                    'minSize'=>"1",
                    'max'=>1,
                    'allow'=>array( 'flv',
                        'mp4', ) ),
                'image'=>array( 'type'=>'image',
                    'name'=>'图片',
                    'maxSize'=>"10485760",
                    'minSize'=>"1",
                    'max'=>10,
                    'allow'=>array( 'jpg',
                        'gif',
                        'png' ) ),
            )
        );
    }
    /**
     * get count resource of a object
     * @param  array $count_resource [description]
     * @return intval                 [description]
     */
    public function getCountResource($content_resources){
        $resource=array();
        $resource_upload=array();
        foreach($content_resources as $res)
        {                                                                                                            
           $resource_upload[]=GxcHelpers::getArrayResourceObjectBinding('resource_upload_'.$res['type']);
        }    
       
       $i=0;
       $count_resource=0;
       foreach($content_resources as $cres){
           $j=1;
           foreach ($resource_upload[$i] as $res_up){                                   
              $j++;
              $count_resource++;
          }
          $i++;
      }
      return array("resource_upload"=>$resource_upload,"count"=>$i);
    }

    /**
     * get all templetes
     * @return array [description]
     */
    public function getTemplates(){
        $templates = Template::find(['type'=>0])->all();
        $newArray = array();
        foreach($templates as $k => $template){
            $newArray[$template->id] = $template->name;
            // $newArray[$k]['name'] = $templete->name;
        }
        return $newArray;
    }
    /**
     * display the content view 
     * @param  arrar $id  object_id
     * @return string
     */
    public function display($page,$pageCount = 0,$url = ''){
        $object_templete = ObjectTemplate::findOne(["object_id"=>$this->object_id]);
        if(empty($object_templete)){
            return "";
        }
        $templete = Template::findOne($object_templete->templete_id);
        if($templete){
            return Yii::$app->cmsRenderer->render($this,$templete->content,array("id"=>$this->object_id,'page'=>$page,'pageCount'=>$pageCount,'url'=>$url));
        }
        throw new HttpException( 404, 'The requested page does not exist.' );
        
    }
    /**
     * get the object term level
     * @param  intaval $id object_id
     * @return array   
     */
    public function getObjectTermById($id){
        // $object_terms = ObjectTerm::model()->findAllByAttributes(array("object_id"=>$id));
        // $ret = array();
        // foreach($object_terms as $key=>$objectterm){
        //     if($objectterm->object_term->level == 1){
        //         continue;
        //     }else{
        //         $ret[$key]["term_id"] = $objectterm->term_id;
        //         $ret[$key]['term_name'] = $objectterm->object_term->name;
        //         $ret[$key]['url'] = $objectterm->object_term->url;
        //     }
        // }
        // return $ret;
        // $criteria = new CDbCriteria;
        // $criteria->condition = "object_id = :object_id";
        // $criteria->params = array(":object_id"=>$id);
        // $criteria->order = "term_id DESC";
        // $criteria->limit = 1;
        $result = ObjectTerm::find()->where(['object_id'=>$id])->orderBy(['term_id'=>SORT_DESC])->limit(1)->one();
        if(empty($result)){
            return "";
        }
        $oterm = new Oterm;
        return $oterm->getLevelByTermId($result->term_id);
    }
    /**
     * get the hot objects
     * @return object
     */
    public function getHotObjects($count){
        $criteria = new CDbCriteria;
        $criteria->condition = "ishot = :ishot AND object_status=:object_status";
        $criteria->params = array(":ishot"=>self::IS_HOT,":object_status"=>ConstantDefine::OBJECT_STATUS_PUBLISHED);
        $criteria->limit = $count;
        $criteria->order = "object_id DESC";

        return Object::model()->findAll($criteria);
    }
    // /**
    //  * get all object of term id (include child id)
    //  * @param  $category_id
    //  * @return array
    //  */
    // public function getAllObjectOfTermIds($category_id,$count){
    //     $nids = ObjectTerm::model()->getObjectIdsByTermId($category_id);
    //     $criteria = new CDbCriteria;
    //     $criteria->condition = "object_status=:status";
    //     $criteria->params = array(":status"=>ConstantDefine::OBJECT_STATUS_PUBLISHED);
    //     $criteria->limit = $count;
    //     $criteria->order = "object_id DESC";
    //     $criteria->addInCondition("object_id",$nids);

    //     $object = Object::model()->findAll($criteria);
    //     return $object;
    // }
    /**
     * get top content by category id
     * @param  intval $category_id content belongs to
     * @return array
     */
    public function getTopContentByTermId($category_id){
        $objects = ObjectTerm::model()->findAllByAttributes(array("term_id"=>$category_id));
        $ids = array();
        foreach($objects as $object){
            $ids[] = $object->object_id;
        }
        $oids = array_unique($ids);
        $criteria = new CDbCriteria;
        $criteria->condition = "istop=:istop AND object_status=:object_status";
        $criteria->params = array(":istop"=>ConstantDefine::OBJECT_ISTOP,":object_status"=>ConstantDefine::OBJECT_STATUS_PUBLISHED);
        $criteria->addInCondition("object_id",$oids);

        return Object::model()->findByAttributes(array(),$criteria);
    }
    /**
     * get is red title Content By TermId 
     * @param  intval $this->categoryid 
     * @param  intval $this->count      
     * @return array                  
     */
    public function getRecommendContentByTermId($category_id,$count){
        $objects = ObjectTerm::model()->findAllByAttributes(array("term_id"=>$category_id));
        $ids = array();
        foreach($objects as $object){
            $ids[] = $object->object_id;
        }
        $oids = array_unique($ids);
        $criteria = new CDbCriteria;
        $criteria->condition = "isred=:isred AND object_status=:object_status";
        $criteria->params = array(":isred"=>ConstantDefine::OBJECT_ISRED,":object_status"=>ConstantDefine::OBJECT_STATUS_PUBLISHED);
        $criteria->limit = $count;
        $criteria->addInCondition("object_id",$oids);

        return Object::model()->findAllByAttributes(array(),$criteria);
    }
    /**
     * publish html page 
     * @return boolean
     */
    public function doPublish(){
        if($this->object_status == ConstantDefine::OBJECT_STATUS_DRAFT || $this->object_status == ConstantDefine::OBJECT_STATUS_PUBLISHED){
            if($this->page > 1){
                for($i=1;$i<=$this->page;$i++){
                  $path = "/a/".date('Y-m-d',strtotime($this->object_date_gmt))."/001".(strtotime($this->object_date_gmt)+$this->object_id);
                  $content = $this->display($i,$this->page,$path);
                  $domain = Yii::$app->getModule("cms")->domain;
                  // $path = "a/".date('Y-m-d',strtotime($this->object_date_gmt))."/001".(strtotime($this->object_date_gmt)+$this->object_id).'-'.$page.".html";
                  if($i == 1){
                      $result = Yii::$app->publisher->saveDomainHtml($domain,$path.".html",$content);
                  }else{
                      $result = Yii::$app->publisher->saveDomainHtml($domain,$path."_".$i.".html",$content);
                  }
                  
                  if($result){
                      $this->object_status = ConstantDefine::OBJECT_STATUS_PUBLISHED;
                      $this->url = $path.".html";
                      $this->save(false);
                      $termCache = isset($this->term_cache) ? unserialize($this->term_cache) : array();
                      $this->firePublished($termCache);
                  }else{
                      Yii::log("published object fail for object:".$this->object_id,CLogger::LEVEL_WARNING);
                  }
                }
            }else{
                $domain = Yii::$app->getModule("cms")->domain;
                $path = "/a/".date('Y-m-d',strtotime($this->object_date_gmt))."/001".(strtotime($this->object_date_gmt)+$this->object_id).".html";
                $content = $this->display(1);
                $result = Yii::$app->publisher->saveDomainHtml($domain,$path,$content);
                if($result){
                    $this->object_status = ConstantDefine::OBJECT_STATUS_PUBLISHED;
                    $this->url = $path;
                    $this->save(false);
                    $termCache = isset($this->term_cache) ? unserialize($this->term_cache) : array();
                    $this->firePublished($termCache);
                    return true;
                }else{
                    Yii::log("published object fail for object:".$this->object_id,CLogger::LEVEL_WARNING);
                }
            }
        }
    }
    /**
     * delete html 
     * @return boolean
     */
    public function doDelete(){
        if($this->object_status == ConstantDefine::OBJECT_STATUS_DELETE){
            $domain = Yii::app()->getModule("cms")->domain;
            $path = "a/".date('Y-m-d',strtotime($this->object_date_gmt))."/001".(strtotime($this->object_date_gmt)+$this->object_id).".html";
            $content = "对不起，您访问的页面已经被删除！";
            $result = Yii::app()->publisher->saveDomainHtml($domain,$path,$content);
            if($result){
                $this->url = "/".$path;
                $this->save(false);
                // $termCache = isset($this->term_cache) ? unserialize($this->term_cache) : array();
                // $this->firePublished($termCache);
                return true;
            }else{
                Yii::log("published object fail for object:".$this->object_id,CLogger::LEVEL_WARNING);
            }
        }
        return false;
    }
    /**
     * get templete content by object id
     * @param   $object_id [description]
     * @return             [description]
     */
    public function getObjectTempleteById($object_id){
        $object_templete = ObjectTemplete::model()->findByAttributes(array("object_id"=>$object_id));
        $templete = Templete::model()->findByPk($object_templete->templete_id);
        if(empty($templete)){
            return false;
        }
        return $templete->content;
    }
    /**
     * get relation news list  
     * @param  intval $object_id   [description]
     * @param  intval $category_id [description]
     * @param  intval $count [description]
     * @return array
     */
    public function getRelationList($object_id,$count){
        $object = Object::findOne($object_id);
        // $category = ObjectTerm::find()->where(['object_id'=>$object_id])->one();
        // if(empty($category)){
        //     return array();
        // }
        // $objects = ObjectTerm::find()->where(['term_id'=>$category->term_id])->all();
        // $ids = array();
        // foreach($objects as $object){
        //     $ids[] = $object->object_id;
        // }
        // $oids = array_unique($ids);
        // $key = array_search($object_id, $oids);
        // unset($oids[$key]);
        // return Object::find()
        //             ->where(['object_id'=>$oids,"object_status"=>ConstantDefine::OBJECT_STATUS_PUBLISHED])
        //             ->limit($count)
        //             ->orderBy(["object_id"=>SORT_DESC])
        //             ->all();
        $keywords = "%{$object->object_keywords}%";
        return Object::find()->where(['like','object_keywords',$keywords])
                             ->andWhere("object_id!={$object->object_id}")
                             ->limit($count)
                             ->orderBy(['object_date'=>SORT_DESC])
                             ->all();
    }
    public function searchGame(){
        $criteria=new CDbCriteria;
        $criteria->order = "object_id DESC";
        $criteria->condition = "object_type = 'game'";

        return new CActiveDataProvider("Object", array(
            'criteria'=>$criteria,
            'pagination' => array(
                    'pageSize' => 20,
                )
        ));
    }
    /**
     * get page title
     */
    public function getTitle(){
        return $this->object_title."_狗民网";
    }
    /**
     * get page keywords
     */
    public function getKeywords(){
        return $this->object_keywords;
    }
    /**
     * get page description
     */
    public function getDescription(){
        return $this->object_description;
    }
    /**
     * get object id
     */
    public function getId(){
        return $this->object_id;
    }
    /**
     * get object term url for object menu display used in fhgame for now
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getTermUrl(){
        $object_term = ObjectTerm::find()->where(array("object_id"=>$this->object_id))->one;
        if(empty($object_term)){
            return "";
        }
        $term = Oterm::findOne($object_term->term_id);
        if(empty($term)){
            return "";
        }
        return "<a class=agray  href={$term->url}>$term->name</a>";
    }
    /**
     * get category id all descendants node id include self id
     * 
     * @param $term_name
     * @return int(id)
     */
    public function getAllChindrenIdByTermId($term_id){
        // var_dump($term_id);exit;
        $category = Oterm::findOne($term_id);
        if(empty($category)){
            return null;
        }
        $descendants=$category->descendants()->all();
        $allterms = array();
        foreach ($descendants as $value) {
            $allterms[] = $value->id;
        }
        // var_dump($allterms);exit;
        array_push($allterms, $term_id);
        // print_r(array_push($allterms, $term_id));exit;
        return $allterms;
    }
    /**
     * fetch all objects by term id 
     * the data include term's children id 
     * @return [type] [description]
     */
    public function fetchObjectsByTermId($term_id,$count,$page){
        $termIds = $this->getAllChindrenIdByTermId($term_id);

        // $criteria = new CDbCriteria;
        // $criteria->addInCondition("term_id",$termIds);
        // $criteria->order = "t.object_id DESC";
        $results = ObjectTerm::find()->andFilterWhere(['in', 'term_id', $termIds])->orderBy("object_id DESC")->All();
        $ids = array();
        foreach($results as $result){
            $ids[] = $result->object_id;
        }
        $ids = array_unique($ids);


        // $criteria = new CDbCriteria;
        // $criteria->alias = "t";

        // $criteria->addInCondition("object_id",$ids);
        // $criteria->order = "t.object_id DESC";
        // $criteria->limit = $count;
        // $criteria->offset = ($page - 1) * $count;
        // 
        return ObjectTerm::find()
                    ->with('objects')
                    ->leftJoin('object o','object_term.object_id = o.object_id')
                    ->andWhere(['o.object_status'=>ConstantDefine::OBJECT_STATUS_PUBLISHED])
                    ->andFilterWhere(['in', 'term_id', $termIds])
                    ->limit($count)
                    ->orderby('o.object_date DESC')
                    ->offset(($page - 1) * $count)
                    ->all();
        
        // return self::find()
        //         ->where("object_status=2")
        //         ->andWhere(['in', 'object_id', $ids])
        //         ->orderBy("object_date DESC")
        //         ->limit($count)
        //         ->offset(($page - 1) * $count)
        //         ->all();
        // return self::model()->findAll($criteria);
    }
    /**
     * get objects count by term_id
     * @return [type] [description]
     */
    public function getObjectsCountByTermId($term_id){
        // $termIds = $this->getAllChindrenIdByTermId($term_id);
        // $criteria = new CDbCriteria;
        // $criteria->alias = "t";
        // $criteria->addInCondition("term_id",$termIds);
        // return ObjectTerm::model()->count($criteria);
        // 
        $termIds = $this->getAllChindrenIdByTermId($term_id);

        // $criteria = new CDbCriteria;
        // $criteria->addInCondition("term_id",$termIds);
        // $criteria->order = "t.object_id DESC";
        $results = ObjectTerm::find()->leftJoin('object o','object_term.object_id = o.object_id')->andWhere(['o.object_status'=>ConstantDefine::OBJECT_STATUS_PUBLISHED])->andFilterWhere(['in', 'term_id', $termIds])->all();
        $ids = array();
        foreach($results as $result){
            $ids[] = $result->object_id;
        }
        $ids = array_unique($ids);
        return count($ids);
    }
    //save object term cache
    public function updateObjectTermCache(){
        $objectterm = new ObjectTerm;
        $current_temrs = $objectterm->getAncestorsIdsByObject($this->object_id);
        $this->term_cache = serialize($current_temrs);
        $this->save(false);
        return true;
    }
    /**
     * get object of search
     * @param  [type] $ids [description]
     * @return [type]      [description]
     */
    public function getResultOfSearch($ids){
        $criteria = new CDbCriteria;
        $criteria->addInCondition("object_id",$ids);
        return self::model()->findAll($criteria);
    }
    /**
     * get object by time
     * @param  [type] $reg_date [description]
     * @param  [type] $num      [description]
     * @return [type]           [description]
     */
    public function getObjectsByTime($req_date,$offset,$num){
        $criteria = new CDbCriteria;
        $begin = $req_date ." 00:00:00";
        $end = date('Y-m-d')." 00:00:00";
        $criteria->condition = "object_date >= :begin AND object_date <= :end AND object_status = :object_status";
        $criteria->params = array(":begin"=>$begin,":end"=>$end,":object_status"=>ConstantDefine::OBJECT_STATUS_PUBLISHED);
        $criteria->offset = $offset;
        $criteria->limit = $num;

        return self::model()->findAll($criteria);
    }
    /**
     * [saveTemplate description]
     * @return [type] [description]
     */
    public function saveTemplate(){
        $obj_tem = new ObjectTemplate;
        $obj_tem->object_id = $this->object_id;
        $obj_tem->templete_id = $this->template_id;
        if($obj_tem->save()){
            return true;
        }
        return false;
    }
    public function getObjecttags(){
        return $this->hasMany(Tag::className(),['id'=>'tag_id'])
                    ->viaTable('tag_relationships',['object_id'=>'object_id']);
    }
    public function getTagsinfo(){
        return $this->hasMany(TagRelationships::className(),['tag_id'=>'id']);
    }
    public function getTermname(){
        return $this->hasOne(Oterm::className(),['id'=>'term_id'])
                    ->via("objectreterm");
    }
    public function getObjectreterm(){
        return $this->hasOne(ObjectTerm::className(),['object_id'=>'object_id']);
    }

} 

    
