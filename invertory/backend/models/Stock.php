<?php
namespace backend\models;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\helpers\BaseArrayHelper;
use backend\components\BackendActiveRecord;

class Stock extends BackendActiveRecord {
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
            [['material_id','storeroom_id','project_id'],'required'],
            [['active','forecast_quantity','actual_quantity','stock_time','delivery'],'safe']
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
     * [getCanUseProjects description]
     * @return [type] [description]
     */
    public function getCanUseMaterial(){
        $rs = Material::find()->all();
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
    public function getProjects(){
        return $this->hasOne(Project::className(),['id'=>'project_id']);
    }
    public function getMaterial(){
        return $this->hasOne(Material::className(),['id'=>'material_id']);
    }
}