<?php
namespace backend\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use backend\models\AdBack;
use backend\models\AdDetail;
use backend\models\Upload;
use backend\components\BackendController;

class AdController extends BackendController{

    public $enableCsrfValidation;

    public static function tableName(){
        return "ad_home_label";
    }

    public function actionIndex(){
        $model = new AdBack;
        return $this->render("index",['model'=>$model]);
    }

    public function actionCreate(){
        $model = new AdBack;
        $model->loadDefaultValues();
        if(isset($_POST)){
            if($model->addAdLabel($_POST)){
                return $this->redirect("/ad/index");
            }
        }
        return $this->render("create",['model'=>$model]);
    }

    public function actionUpdate(){
        $post = AdBack::findOne($_GET['id']);
        if (!$post) {
            throw new NotFoundHttpException();
        }
        if($post->updateAdLabel($_GET['id'])){
            return $this->redirect("/ad/index");
        }
        return $this->render('create', ['model' => $post]);
    }

    public function actionDelete(){
        if(isset($_GET['id'])){
            $model = new AdBack;
            if($model->deleteAdLabel($_GET['id'])){
                return $this->redirect("/ad/index");
            }
        }
        return $this->redirect("/ad/index");
    }

    public function actionAdetail(){
        if(isset($_GET['id'])){
            $model = new AdDetail;
            $model->loadDefaultValues();
            if(!empty($_POST)){
                if($model->addAdDetail($_POST)){
                    return $this->redirect(["adetail",'id'=>$_GET['id']]);
                }
            }
            return $this->render("createdetail",['model'=>$model,'isNew'=>true,'label_id'=>$_GET['id']]);
        }
        throw new NotFoundHttpException();
    }

    public function actionUpdetail(){
        $post = AdDetail::findOne($_GET['id']);
        if (!$post) {
            throw new NotFoundHttpException();
        }
        if($post->updateAdDetail($_GET['id'])){
            return $this->redirect(["adetail",'id'=>$post->label_id]);
        }
        return $this->render('createdetail', ['model' => $post,'isNew'=>false,'label_id'=>$post->label_id]);
    }

    public function actionDeldetail(){
        if(isset($_GET['id'])){
            $model = new AdDetail;
            if($model->deleteAdDetail($_GET['id'])){
                return $this->redirect(Yii::$app->request->getReferrer());
            }
        }
        return $this->redirect(Yii::$app->request->getReferrer());
    }

    public function actionCheck(){
        if(!empty($_GET['id']) && !empty($_GET['act'])){
            $model = new AdBack;
            $model->getChangeStatus($_GET['id'],$_GET['act']);
        }
        return $this->redirect(Yii::$app->request->getReferrer());
    }

    public function  actionUploadfile(){
        $this->enableCsrfValidation = false;
        if($_FILES){
            $model = new Upload;
            $result = $model->uploadImage($_FILES,true,'ad');
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