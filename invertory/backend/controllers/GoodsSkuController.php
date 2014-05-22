<?php
/**
 * user: liding
 * date: 14-4-29
 */

namespace backend\controllers;

use yii\web\Controller;
use backend\models\AttributeValueBack;
use yii\web\NotFoundHttpException;

class GoodsSkuController extends Controller{


    public function actionAdd()
    {
        $goods_id = $_GET['id'];
        $type_id = $_GET['type_id'];

        //获取商品的SKU属性
        $attributeValueBackObj = new AttributeValueBack();
        $attributeValueSku = $attributeValueBackObj->getArrayAttributeValue($type_id,$goods_id);

        $newAttributes =  $this->tidyAttributes($attributeValueSku);

    }


    private function tidyAttributes($params)
    {
        //格式化成所需要的数组形式
        foreach($params as $value){
            $attributeNum[] = $value['attr_id'];
            $attributes[$value['attr_id']][] = $value['attribute_value'];
        }
        //获取属性的层数
        $newAttributeNum = array_unique($attributeNum);
        $num = count($newAttributeNum);

        if($num >2){
            throw new NotFoundHttpException('此版本暂时不支持3以上层属性，请修改');
        }elseif($num == 2){
            foreach($attributes[$newAttributeNum[0]] as $val){
                foreach($attributes[$newAttributeNum[1]] as $v){
                    $key = $val.','.$v;
                    $val = $newAttributeNum[0] .','.$newAttributeNum[1];
                    $newAttributes [$key] = $val;
                }
            }
        }elseif($num == 1){
            foreach($newAttributeNum as $value){
                foreach($attributes[$value] as $val){
                    $newAttributes [$val] = $value;
                }
            }
        }else{
            $newAttributes = [];
        }
        echo "<pre>";
        var_dump($newAttributes);
        return $newAttributes;
    }

} 