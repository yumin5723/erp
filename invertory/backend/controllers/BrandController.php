<?php
namespace backend\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use backend\models\BrandBack;
use backend\models\CategoryBack;
use backend\models\Upload;
use backend\components\BackendController;

class BrandController extends BackendController{
    
    public $enableCsrfValidation;

    public function actionIndex(){
        $model = new BrandBack;
        return $this->render("index",['model'=>$model]);
    }

    public function actionCreate(){
        $model = new BrandBack;
        if(isset($_POST)){
            if($model->addBrand($_POST)){
                return $this->redirect("/brand/index");
            }
        }
        return $this->render("create",['model'=>$model,'isNew'=>true,'category'=>CategoryBack::getCatIds()]);
    }

    public function actionUpdate(){
        $post = BrandBack::findOne($_GET['id']);
        if (!$post) {
            throw new NotFoundHttpException();
        }
        if($post->updateBrand($_GET['id'])){
            return $this->redirect("/brand/index");
        }
        return $this->render('create',['model'=>$post,'isNew'=>false,'category'=>CategoryBack::getCatIds()]);
    }

    public function actionDelete(){
        $model = new BrandBack;
        $id = intval($_GET['id']);
        $model->deleteBrand($id);
        return $this->redirect("/brand/index");
    }

    public function actionView(){
        $id = intval($_GET['id']);
        $model = new BrandBack;
        $rs = $model->findBrand($id);
        return $this->render("view",['model'=>$rs]);
    }

    public function  actionUploadfile(){
        $this->enableCsrfValidation = false;
        if($_FILES){
            $model = new Upload;
            $result = $model->uploadImage($_FILES,true,'brands');
            if($result[0] == true){
echo <<<EOF
    <script>parent.stopSend("{$result[1]}","{$result[2]}","{$result[3]}");</script>
EOF;
            }else{
echo <<<EOF
    <script>alert("{$result[1]}");</script>
EOF;
            }
        }
    }

}