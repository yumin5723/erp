<?php
/**
 * User: liding
 * Date: 14-4-10
 * Time: 12:33
 */

namespace backend\controllers;

use yii\web\Controller;
use backend\models\GoodsTypeBack;
use backend\models\AttributesBack;
use yii\web\NotFoundHttpException;

class TypeController extends Controller {

    /**
     * 获取所有的类型
     * @return string
     */
    public function actionIndex()
    {
        $model = new GoodsTypeBack();
        return $this->render('index',['model'=>$model]);
    }


    /**
     * 创建类型
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new GoodsTypeBack();
        if(!empty($_POST)){
            $result = $model->addGoodsType($_POST);
            if($result){
                return $this->redirect("/type/index");
            }else{
                echo "<script>alert('添加失败')</script>";
            }
        }
        return $this->render('create',[
            'model'=>$model,
            'groupList' => $model->groupList(),
            'cpList' => $model->cpList()
        ]);
    }


    /**
     * 修改类型状态
     */
    public function actionChange()
    {
        $url = $_SERVER['HTTP_REFERER'];
        $model = new GoodsTypeBack();
        if(!$model->changeTypeStatus()){
           echo "<script>alert('修改失败')</script>";
        }
        $this->redirect($url);
    }


} 