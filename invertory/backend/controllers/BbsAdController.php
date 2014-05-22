<?php
namespace backend\controllers;

use Yii;
use backend\models\BbsAd;
use backend\models\Upload;
use backend\models\WhiteList;
use backend\components\BackendController;

class BbsAdController extends BackendController{
    public $enableCsrfValidation;
    public function actionAdmin(){
        $model = new BbsAd;
        if($model->getCount()<3){
            if($model->load($_POST)){
                if($model->validate() && $model->save()){
                    return $this->redirect("/bbs-ad/admin");
                }
            }
        }else{
            Yii::$app->session->setFlash('warning', '最多添加三个广告，如果少于三个则广告不予显示生效!');
        }
        return $this->render('index',['model'=>$model,'isNew'=>true,'count'=>$model->getCount()]);
    }

    public function actionIndex(){
        $type = 1;
        $model = new BbsAd;
            if($model->load($_POST)){
                if($model->validate() && $model->save()){
                    return $this->redirect("/bbs-ad/index");
                }else{
                    Yii::$app->session->setFlash('error', '添加失败!');
                }
            }
        return $this->render('index_ad',['model'=>$model,'isNew'=>true,'type'=>$type]);
    }

    public function actionUpdate($id){
        $post = BbsAd::findOne($id);
        if (!$post) {
            throw new NotFoundHttpException();
        }
        if($post->updateAd($id)){
            if($post->type){
                    return $this->redirect("/bbs-ad/index");
            }
            return $this->redirect("/bbs-ad/admin");
        }
        if($post->type){
            return $this->render('index_ad',['model'=>$post,'isNew'=>false,'type'=>$post->type]);
        }
        return $this->render('index',['model'=>$post,'isNew'=>false,'count'=>$post->getCount()]);
    }

    public function actionDelete($id){
        $model = BbsAd::findOne($id);
        $model->delete();
        if($model->type){
            return $this->redirect("/bbs-ad/index");
        }
        return $this->redirect("/bbs-ad/admin");
    }

    public function  actionUploadfile(){
        $this->enableCsrfValidation = false;
        if($_FILES){
            $model = new Upload;
            $result = $model->uploadImage($_FILES,false,'bbs-ad');
            if($result[0] == true){
echo <<<EOF
    <script>parent.stopSend("{$result[1]}","{$result[2]}");</script>
EOF;
            }else{
echo <<<EOF
    <script>alert("{$result[1]}");</script>
EOF;
            }
        }
    }

    public function actionWhitelist(){
        $model = new WhiteList;
        if($model->load($_POST)){
            if($model->validate() && $model->save()){
                return $this->redirect("/bbs-ad/whitelist");
            }
        }
        return $this->render('whitelist',['model'=>$model]);
    }

    public function actionUpdatekey($id){
        $post = WhiteList::findOne($id);
        if (!$post) {
            throw new NotFoundHttpException();
        }
        if($post->updateKeyword($id)){
            return $this->redirect("/bbs-ad/whitelist");
        }
        return $this->render('whitelist',['model'=>$post]);
    }

    public function actionDeletekey($id){
        $model = WhiteList::findOne($id);
        $model->delete();
        return $this->redirect("/bbs-ad/whitelist");
    }
}
