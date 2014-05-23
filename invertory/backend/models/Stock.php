<?php
namespace backend\models;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\helpers\BaseArrayHelper;

class Stock extends ActiveRecord {
    public $upload;
    /**
     * function_description
     *
     *
     * @return
     */
    public static function tableName() {
        return 'stock';
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function rules() {
        return [
            [['name','code','storeroom_id','owner_id','project_id'],'required'],
            [['active','english_name','forecast_quantity','actual_quantity','stock_time','delivery','image'],'safe']
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
    /**
     * [getCanUseProjects description]
     * @return [type] [description]
     */
    public function getCanUseProjects(){
        $rs = Project::find()->all();
        $arr = [];
        if($rs){
            foreach($rs as $key=>$v){
                $arr[$v['id']]=$v['name'];
            }

        }
        return $arr;
    }
    /**
     * [getCanUseStorerooms description]
     * @return [type] [description]
     */
    public function getCanUseStorerooms(){
        $rs = Storeroom::find()->all();
        $arr = [];
        if($rs){
            foreach($rs as $key=>$v){
                $arr[$v['id']]=$v['name'];
            }

        }
        return $arr;
    }
    /**
     * [getCanUseStorerooms description]
     * @return [type] [description]
     */
    public function getCanUseOwners(){
        $rs = Owner::find()->all();
        $arr = [];
        if($rs){
            foreach($rs as $key=>$v){
                $arr[$v->id]=$v->english_name;
            }

        }
        return $arr;
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
        if (is_array($this->image)) {
            $this->image = serialize($this->image);
        }
        return parent::beforeSave($insert);
    }
    /**
     * function_description
     *
     *
     * @return
     */
    public function afterFind() {
        if (!is_array($this->image)) {
            @$this->image = unserialize($this->image);
        }
    }
}