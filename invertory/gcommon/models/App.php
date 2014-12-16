<?php

class App extends CActiveRecord {

    /**
     * model
     *
     * @param $className:
     *
     * @return CPUser the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * get model table name
     *
     *
     * @return string the associated database table name
     */
    public function tableName() {
        return 'app';
    }

    /**
     * get model rules
     *
     *
     * @return array validation rules for model attributes
     */
    public function rules() {
        $rules =  array(
            array('app_name, short_name, cp_id, charge_chn', 'required'),
            array('charge_url', 'url', 'allowEmpty'=>true),
            array('id, cp_id, app_name, short_name, created, modified', 'safe', 'on'=>'search'),
        );
        return $rules;
    }
    /**
     * get behaviors
     *
     *
     * @return
     */
    public function behaviors() {
        return array(
            'CTimestampBehavior' => array(
                'class'               => 'zii.behaviors.CTimestampBehavior',
                'createAttribute'     => 'created',
                'updateAttribute'     => 'modified',
                'timestampExpression' => 'date("Y-m-d H:i:s")',
                'setUpdateOnCreate'   => true,
            ),
        );
    }
    /**
     * get model relational rules
     *
     *
     * @return array relational rules
     */
    public function relations() {
        return array();
    }

    /**
     * get attribute labels
     *
     *
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            "app_name" => "应用名称",
            "short_name" => "简称",
            "charge_chn" => '充值接口',
            "charge_url" => '充值接口地址',
            "created" => '创建时间',
            "modified" => '最后修改',
        );
    }
    /**
     * function_description
     *
     * @param $attributes:
     *
     * @return
     */
    public function updateAttrs($attributes) {
        $attrs = array();
        if (!empty($attributes['app_name']) && $attributes['app_name'] != $this->app_name) {
            $attrs[] = 'app_name';
            $this->app_name = $attributes['app_name'];
        }
        if ($this->validate($attrs)) {
            return $this->save(false);
        } else {
            return false;
        }
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

        $criteria->compare('id',$this->id);
        $criteria->compare('cp_id',$this->cp_id);
        $criteria->compare('app_name',$this->app_name,true);
        $criteria->compare('short_name',$this->short_name,true);
        $criteria->compare('created',$this->created,true);
        $criteria->compare('modified',$this->modified,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
}
