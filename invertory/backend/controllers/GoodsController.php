<?php
namespace backend\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use backend\models\Species;
use backend\models\GoodsBack;
use backend\models\AttributesBack;
use backend\models\GoodStatus;
use yii\web\UploadedFile;
use backend\models\Upload;
use backend\models\CategoryBack;
use backend\models\search\GoodsSearch;
use backend\components\BackendController;


class GoodsController extends BackendController{

    public $enableCsrfValidation;

    public function actionIndex(){
        $searchModel = new GoodsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'model' => $searchModel,
        ]);
        // $model = new GoodsBack;
        // return $this->render('index',['model'=>$model]);
    }

    /**
     * 商品添加操作
     * @return string|\yii\web\Response
     */
    public function actionCreate(){
        $model = new GoodsBack;
        $model->loadDefaultValues();
        // $model->is_real=1;
        $category = CategoryBack::getCatIds(); //获取分类数据
        //获取所有的类型
        $attributesBackObj = new AttributesBack();
        $types = $attributesBackObj->attributeCategory();

        $express = $model->getExpressTpl();
        if(isset($_POST) && !empty($_POST)){
            if($model->addGoods($_POST)){
                return $this->redirect("/goods/index");
            }
        }

        return $this->render("create",[
            'model'=>$model,
            'isNew'=>true,
            'brands'=>$model->getBrands(),
            'category'=>$category,
            'express'=>$express,
            'types' => $types,
        ]);
    }

    /**
     * 商品修改
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionUpdate()
    {
        $post = GoodsBack::findOne($_GET['id']);
        if (!$post) {
            throw new NotFoundHttpException();
        }

        if($post->updateGoods($_GET['id'])){
            return $this->redirect("/goods/index");
            // return $this->redirect(['view', 'id' => $post->goods_id]);
        }

        //获取所有的类型
        $attributesBackObj = new AttributesBack();
        $types = $attributesBackObj->attributeCategory();

        // $post->goods_img = "";
        return $this->render('create', [
            'model' => $post,
            'isNew'=>false,
            'brands'=>$post->getBrands(),
            'category'=>CategoryBack::getCatIds(),
            'express'=>$post->getExpressTpl(),
            'types' => $types,
        ]);

    }

    // public function actionDelete(){
    //     $model = new GoodsBack;
    //     $id = intval($_GET['id']);
    //     $model->deleteGoods($id);
    //     return $this->redirect("/goods/index");

    // }

    public function actionView(){
        $model = new GoodsBack;
        $rs = $model->showOne($_GET['id']);
        if($rs){
            return $this->render("view",['model'=>$rs,'category'=>$model->getCatIds()]);
        }else{
            throw new NotFoundHttpException();
        }
    }


    public function actionAddmore(){
        $model = new AttributesBack;
        $goods_id = $_GET['id'];
        if(isset($_POST) && !empty($_POST)){
            if($model->addAttributes($_POST)){
                return $this->redirect("/goods/addmore/{$_GET['id']}");
            }
        }
        return $this->render("attribute",['model'=>$model,'goods_id'=>$goods_id]);
    }


    public function actionCheck(){
        $goods = GoodsBack::findOne($_GET['id']);
        if($goods){
            $model = GoodStatus::findOne(['goods_id'=>$_GET['id']])?:new GoodStatus;
            if(isset($_POST) && !empty($_POST)){
                if($model->addStatus(($_GET['id']))){
                    return $this->redirect("/goods/checklist");
                }
            }
            return $this->render("status",['model'=>$model,'goods_id'=>$goods->goods_id,'goods_name'=>$goods->goods_name,'activity'=>$goods->getActivity()]);
        }else{
            throw new NotFoundHttpException();
        }
    }


    public function actionUpdatest(){
        $model = GoodStatus::findOne($_GET['id']);
        if($model){
            $goods = GoodsBack::findOne($model->goods_id);
            if(isset($_POST) && !empty($_POST)){
                if($model->addStatus(($_GET['id']))){
                    return $this->redirect("/goods/checklist");//1396332357
                }
            }
            return $this->render("status",['model'=>$model,'goods_id'=>$model->goods_id,'goods_name'=>$goods->goods_name,'activity'=>$goods->getActivity()]);
        }else{
            throw new NotFoundHttpException();
        }
    }


    public function actionDelete(){
        $model = new GoodStatus;
        $id = intval($_GET['id']);
        $model->deleteGoods($id);
        return $this->redirect("/goods/checklist");

    }


    public function actionChecklist(){
        $goods = new GoodsBack;
        $model = new GoodStatus;
        return $this->render("check",['model'=>$model,'goods'=>$goods]);
    }


    public function actionCheckall(){
        $model = new GoodStatus;
        if(isset($_GET['act'])){
            $nums = $model->checkAllStatus($_GET['act']);
            echo $nums;exit;
        }
    }

    /**
     * upload image from ckeditor
     * @return [type] [description]
     */
    public function actionUpload(){
        $this->enableCsrfValidation = false;
        $fn=$_GET['CKEditorFuncNum'];

        if($_FILES){
            $model = new Upload;
            $result = $model->uploadImage($_FILES);
            if($result[0] == true){
                $message = "上传成功";
                $fileurl = $result[1];
            }else{
                $fileurl = "";
                $message = $result[1];
            }
            $str='<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction('.$fn.', \''.$fileurl.'\', \''.$message.'\');</script>';
            exit($str);
        }
    }
    /**
     * upload image from form
     * @return [type] [description]
     */
    public function actionUploadfile(){
        $this->enableCsrfValidation = false;
        if($_FILES){
            $model = new Upload;
            $result = $model->uploadImage($_FILES,true);
            if($result[0] == true){
                    echo <<<EOF
            <script>parent.stopSend("{$result[1]}","{$result[2]}","{$result[3]}");</script>
EOF;
                 //return "<script>parent.stopSend('$result[1]','$result[2]','$result[3]')</script>";
            }else{
                echo <<<EOF
            <script>alert("{$result[1]}");</script>
EOF;
                // return  '<script>alert("'.$result[1].'");</script>';
            }
        }
    }

}