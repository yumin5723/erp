<?php

class ObjectBak extends CActiveRecord {
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
        return 'object_bak';
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function rules() {
        return array(
            array('object_id,object_modified_uid,object_content,object_title,object_excerpt,object_modified,object_author_name,tags','safe'),
            array('id, object_id, object_author_name, object_title, created, modified', 'safe', 'on'=>'search'),
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
     * when user update content backup that
     * @param  array $data need backup data
     * @return boolean
     */
    public function backupContent($data){
        $model = new self;
        $model->object_id = $data->object_id;
        $model->object_modified_uid = Yii::app()->user->id;
        $model->object_content = $data->object_content;
        $model->object_title = $data->object_title;
        $model->object_excerpt = $data->object_excerpt;
        $model->object_author_name = $data->object_author_name;
        $model->tags = $data->tags;
        $model->save();
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
        $criteria->order = "id desc";
        $criteria->compare('id',$this->id);
        $criteria->compare( 'object_id', $this->object_id, true );
        $criteria->compare( 'object_author_name', $this->object_author_name, true );
        $criteria->compare( 'object_title', $this->object_title, true );

        return new CActiveDataProvider(get_class($this), array(
            'criteria'=>$criteria,
            'pagination'=>array(
                              'pageSize'=>20,
                          ),
        ));
    }
}