<?php
/**
 * user: liding
 * date: 14-4-22
 */

namespace backend\controllers;

use backend\models\AttributesBack;
use yii\web\Controller;
use backend\models\AttributeValueBack;
use backend\models\AttributeBack;

class AttributeValueController extends Controller{


    /**
     * 添加商品属性值
     */
    public function actionAdd()
    {
        $goods_id = $_GET['id'];
        $type_id = $_GET['type_id'];
        $attribute  = $this->getAttribute($type_id);
        $hint = '';
        if(!empty($_POST)){
            $model = new AttributeValueBack();
            $result = $model->addAttributeValue($_POST,$goods_id);
            //添加成功后跳转到添加SKU操作
            if($result['status'] === 1){
                $this->redirect('goods-sku/add?id='.$goods_id);
            }else{
                //添加失败 则把提示显示
                $hint = $result['hint'];
            }
        }
        return $this->render('create',[
            'attribute' =>$attribute,
            'hint' => $hint,
        ]);
    }

    public function actionUpdate()
    {
        $goods_id = $_GET['id'];
        $type_id = $_GET['type_id'];
    }


    /**
     * 根据商品选择的类型获取对应的属性
     */
    private function getAttribute($type)
    {
        if(!isset($type) && empty($type)){
            return false;
        }

        $model = new AttributesBack();
        $result = $model->findAttributeArray($type);
        return $result;
    }
} 