<?php
/**
 * user: liding
 * date: 14-4-14
 */

namespace backend\controllers;

use yii\web\Controller;
use backend\models\AttributesBack;
class AttributeController extends Controller{

    public $sidebars = [
        [
            'name' => '属性列表',
            'icon' => 'tasks',
            'url' => '/attribute/index',
        ],
        [
            'name' => '属性添加',
            'icon' => 'tasks',
            'url' => '/attribute/add',
        ],
    ];

    /**
     * 获取全部属性
     */
    public function actionIndex()
    {
        $model = new AttributesBack();
        $model->goods_type_id = -1;
        return $this->render('index',[
            'model' => $model,
        ]);
    }

    /**
     * 添加属性
     * @return string
     */
    public function actionAdd()
    {
        $model = new AttributesBack();
        $hint = '';
        if(!empty($_POST)){ //添加操作
            $result = $model->addAttributes($_POST);
            if($result['status'] === 1){ //添加成功
                $model->goods_type_id = $result['type_id'];
                return $this->render('index',[
                    'model' => $model,
                    'attributeType' => $model->attributeType(),
                    'hint' => $result['hint'],
                ]);
            }else{
                $hint = $result['hint'];
            }
        }
        //加载模版
        return $this->render('create',[
            'model' => $model,
            'attributeType' => $model->attributeType(),
            'attributeCategory' =>$model->attributeCategory(),
            'hint' => $hint,
        ]);
    }


    /**
     * 显示和添加属性
     */
    public function actionAddattribute()
    {
        $model = new AttributesBack();
        $hint = '';
        if(isset($_GET['id'])){
            $model->goods_type_id = $_GET['id'];
            $type_id = $_GET['id'];
        }

        if(!empty($_POST)){
            $result = $model->addAttributes($_POST);
            $type_id = $result['type_id'];
            $model->goods_type_id = $result['type_id'];
            $hint = $result['hint'];
        }
        return $this->render('attribute',[
            'model' => $model,
            'type_id' => $type_id,
            'attributeType' => $model->attributeType(),
            'hint' =>$hint
        ]);
    }


    /**
     * 修改属性
     */
    public function actionUpdate()
    {
        $model = new AttributesBack();
        $hint = '';
        $model->attribute_id = $_GET['id'];
        //修改操作
        if(!empty($_POST)){
            $result = $model->updateAttribute($_POST);
            if($result['status'] === 1){
                $model->goods_type_id = $result['type_id'];
                return $this->render('index',[
                    'model' => $model,
                    'attributeType' => $model->attributeType(),
                    'hint' =>$result['hint']
                ]);
            }else{
                $hint = $result['hint'];
            }
        }
        //加载模版
        return $this->render('create',[
            'model' => $model->findAttribute(),
            'attributeType' => $model->attributeType(),
            'attributeCategory' =>$model->attributeCategory(),
            'hint' => $hint,
        ]);
    }


    /**
     * 删除属性
     */
    public function actionDeleteattribute()
    {
        $model = new AttributesBack();
        $model->attribute_id = $_GET['id'];
        $result = $model->deleteAttribute();
        $model->goods_type_id = $result['type_id'];
        return $this->render('index',[
            'model' => $model,
            'attributeType' => $model->attributeType(),
            'hint' =>$result['hint']
        ]);
    }
} 