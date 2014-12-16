<?php

/**
 * This is the model class for table "{{object_resource}}".
 *
 * The followings are the available columns in table '{{object_resource}}':
 * @property string $object_id
 * @property string $resource_id
 * @property integer $resource_order
 * @property string $description
 */
class ObjectResource extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @return ObjectResource the static model class
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
        return 'object_resource';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('resource_order', 'numerical', 'integerOnly'=>true),
            array('object_id, resource_id', 'length', 'max'=>20),
            array('description,type', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('object_id, resource_id, resource_order, description,type', 'safe', 'on'=>'search'),
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
            'resource' => array(self::BELONGS_TO,'Resource','resource_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'object_id' =>  t('cms','Object'),
            'resource_id' =>  t('cms','Resource'),
            'resource_order' =>  t('cms','Resource Order'),
            'description' =>  t('cms','Description'),
            'type' =>  t('cms','Type'),
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

        $criteria->compare('object_id',$this->object_id,true);
        $criteria->compare('resource_id',$this->resource_id,true);
        $criteria->compare('resource_order',$this->resource_order);
        $criteria->compare('description',$this->description,true);
        $criteria->compare('type',$this->type,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
    /**
     * save post resources from create object form in table object_resource 
     * @param array content_resources the articel allow upload types
     * @param array resource_upload   the resources from post
     * @param intval object_id  
     * @return [type] [description]
     */
    public function saveResourceForObject($content_resources,$resource_upload,$object_id){
        $i=0;
        $count_resource=0;
        foreach($content_resources as $cres){
             $j=1;
             foreach ($resource_upload[$i] as $res_up){
                $obj_res = new ObjectResource;
                $obj_res->resource_id=$res_up['resid'];
                $obj_res->object_id=$object_id;
                $obj_res->description='';
                $obj_res->type=$cres['type'];
                $obj_res->resource_order=$j;
                
                $obj_res->save();
                $j++;
                $count_resource++;
            }
            $i++;
        }
        return true;
    }
}