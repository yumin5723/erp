<?php
namespace customer\models;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\helpers\BaseArrayHelper;

class Storeroom extends ActiveRecord {
    /**
     * function_description
     *
     *
     * @return
     */
    public static function tableName() {
        return 'storeroom';
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function rules() {
        return [
            [['name','level'],'required'],
            [['address','contact','phone'],'safe'],
        ];
    }
    public function behaviors()
    {
        return BaseArrayHelper::merge(
            parent::behaviors(),
            [
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
    public function updateAttrs($attributes){
        $attrs = array();
        if (!empty($attributes['name']) && $attributes['name'] != $this->name) {
            $attrs[] = 'name';
            $this->name = $attributes['name'];
        }
        if (!empty($attributes['level']) && $attributes['level'] != $this->level) {
            $attrs[] = 'level';
            $this->level = $attributes['level'];
        }
        if (!empty($attributes['address']) && $attributes['address'] != $this->address) {
            $attrs[] = 'address';
            $this->address = $attributes['address'];
        }
        if (!empty($attributes['contact']) && $attributes['contact'] != $this->contact) {
            $attrs[] = 'contact';
            $this->contact = $attributes['contact'];
        }
        if (!empty($attributes['phone']) && $attributes['phone'] != $this->phone) {
            $attrs[] = 'phone';
            $this->phone = $attributes['phone'];
        }
        if ($this->validate($attrs)) {
            return $this->save(false);
        } else {
            return false;
        }
    }
    public function attributeLabels(){
        return [
            'name'=>'仓库名',
            'address'=>'仓库地址',
            'level'=>'仓库等级',
            'contact'=>'联系人',
            'phone'=>'联系电话',
        ];
    }
}