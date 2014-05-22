<?php
/**
 * user: liding
 * date: 14-4-10
 */

namespace backend\models;

use common\models\AttributeValue;

class AttributeValueBack extends AttributeValue{


    /**
     * 批量添加属性值
     * @param $params 需要添加的数据
     * @param $goods_id 商品ID
     * @return array
     * [
     *  status 1 成功 0失败
     *  hint 提示信息
     * ]
     */
    public function addAttributeValue($params,$goods_id)
    {
        $formName = $this->formName();
        $attributeValue = $params[$formName];
        $attributeData =[];
        $message = [];
        //组合需要添加的数据
        foreach($attributeValue as $key=>$value){
            if(!empty($value)){
                if(is_array($value)){
                    foreach($value as $val){
                        if(!empty($val)){
                            $data[] = '';
                            $data[] = $key;
                            $data[] = $goods_id;
                            $data[] = $val;
                        }
                    }
                }else{
                    $data[] = '';
                    $data[] = $key;
                    $data[] = $goods_id;
                    $data[] = $value;
                }
                $attributeData[] =$data;
                unset($data);
            }
        }
        if(empty($attributeData)){
            $message['status'] = 0;
            $message['hint'] = '数据为空，添加失败';
            return $message;
        }
        //批量添加数据 返回影响行数
        $rows = static::getDb()
                ->createCommand()
                ->batchInsert(static::tableName(),$this->attributes(),$attributeData)
                ->execute();
        if($rows){
            $message['status'] = 1;
            $message['hint'] = '商品属性值添加成功';
        }else{
            $message['status'] = 0;
            $message['hint'] = '商品属性值添加失败';
        }
        return $message;
    }


    public function updateAttributeValue()
    {

    }

    /**
     * 以数组形式返回商品的SKU属性
     * @param $typeId 商品的类型ID
     * @param $goodsId 商品ID
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getArrayAttributeValue($typeId,$goodsId)
    {
        $result = static::find()
                ->select(['av.attr_id', 'av.attribute_value'])
                ->from(['attribute_value AS av' , 'attribute AS a'])
                ->where('av.attr_id = a.attr_id')
                ->andwhere(['a.attr_type' => 1 ,'a.attr_status' =>1 , 'av.goods_id' => $goodsId , 'a.type_id' => $typeId])
                ->orderBy('a.sort_order DESC')
                ->asArray()
                ->all();
        return $result;
    }
} 