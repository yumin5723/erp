<?php

class CategoryTemplete extends CActiveRecord {

    const STATUS_PARSEING = 0;
    const STATUS_ENABLE = 1;
    const STATUS_PARSE_ERROR = 2;
    public $upload;
    /**
     * function_description
     *
     * @param $className:
     *
     * @return
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function tableName() {
        return 'category_templete';
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function rules() {
        return array(
            array('name','required'),
            array('upload', 'file','allowEmpty'=>false,'types'=>'rar','maxSize'=>1024 * 1024 * 4,'tooLarge'=>'The file was larger than 4MB. Please upload a smaller file.'),
            array('rar_file,content,status','safe'),
        );
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
        return CMap::mergeArray(
            parent::behaviors(),
            array(
              'CTimestampBehavior' => array(
                    'class'               => 'zii.behaviors.CTimestampBehavior',
                    'createAttribute'     => 'created',
                    'updateAttribute'     => 'modified',
                    'timestampExpression' => 'date("Y-m-d H:i:s")',
                    'setUpdateOnCreate'   => true,
                ),
           )
        );
    }
    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;
        $criteria->compare( 'id', $this->id, true );
        $criteria->compare( 'name', $this->name, true );
        $criteria->compare( 'rar_file', $this->rar_file, true );

        $sort = new CSort;
        $sort->attributes = array(
            'id',
        );
        $sort->defaultOrder = 'id DESC';


        return new CActiveDataProvider( $this, array(
                'criteria'=>$criteria,
                'sort'=>$sort
            ) );
    }
    /**
     * save new Rar file when create page
     *
     *
     * @return array(boolean result, MError err)
     * result for if new file have saved success.
     * err is the error for not created or saved. if saved success, the err is null
     */
    public function saveRarFile($files){
        $tmp_file = $files['CategoryTemplete']['tmp_name']['upload'];
        $real_file = $files['CategoryTemplete']['name']['upload'];
        $file = new UploadFile($tmp_file,$real_file,"categorytemplete");
        $uri = $file->get_file_uri();
        if($uri == ""){
            return false;
        }
        $this->rar_file = Yii::app()->params['resource_folder'].$uri;
        return true;
    }
   /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'name' => '模板名字',
            'upload' => '上传',
            'rar_file' => '路径',
            'status' => "状态",
            'content' => "内容",
        );
    }
    /**
     * get templete status
     *
     * @return array
     */
    public function getPageStatus() {
        $templete_status = array(
            "0"=>"解析中",
            "1"=>"可用",
            "2"=>"解析错误",
        );
        return $templete_status;
    }
    /**
     * Convert from value to the String of the Object Status
     *
     * @param type    $value
     */
    public static function convertTempleteStatus( $value ) {
        $status = self::model()->getPageStatus();
        if ( isset( $status[$value] ) ) {
            return $status[$value];
        } else {
            return Yii::t( 'cms', 'undefined' );
        }
    }

    /**
     * check template
     *
     *
     * @return
     */
    protected function check() {
        include_once(Yii::getPathOfAlias("gcommon.lib")."/simple_html_dom.php");
        $html = str_get_html($this->content);
        $c = $html->find("[data-widget=content]");
        unset($html);
        gc_collect_cycles();
        return count($c) == 1;
    }


    /**
     * function_description
     *
     *
     * @return boolean
     */
    public function parse() {
        if ($this->status != self::STATUS_PARSEING) {
            return true;
        }
        try {
            $files = Yii::app()->publisher->parseEntirePage($this->rar_file);
            $this->content=$files['html'];
            if ($this->check()) {
                $this->status = self::STATUS_ENABLE;
            } else {
                $this->status = self::STATUS_PARSE_ERROR;
            }
            return $this->save(false);
        } catch (Exception $e) {
            Yii::log("Parse categorytemplete error for categorytemplete: ".$this->id.", with error: ".$e->getMessage(), CLogger::LEVEL_ERROR);
            $this->content = "";
            $this->status = self::STATUS_PARSE_ERROR;
            $this->save(false);
            return true;
        }
    }
}