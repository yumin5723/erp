<?php
namespace customer\models;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use customer\components\CustomerActiveRecord;

class StockTotal extends CustomerActiveRecord {
    /**
     * function_description
     *
     *
     * @return
     */
    public static function tableName() {
        return 'stock_total';
    }
    /**
     * [updateTotal description]
     * @param  [type] $material_id [description]
     * @return [type]              [description]
     */
    public static function updateTotal($material_id,$count){
    	$material = self::findOne($material_id);
    	if(empty($material)){
    		$model = new self;
    		$model->material_id = $material_id;
    		$model->total = $count;
    		$model->save();
    		return true;
    	}else{
    		$material->total = $material->total + $count;
    		$material->save();
    	}
    	return true;
    }
    public function getMaterial(){
    	return $this->hasOne(Material::className(),['id'=>'material_id']);
    }
    public function getLink(){
    	return '
            return \yii\helpers\Html::a("查看明细","/stock/list?StockSearch[material_id]=$model->material_id");
        ';
    }
    public function attributeLabels(){
        return [
            'material_id'=>'物料',
            'total'=>'现有库存',
        ];
    }
}