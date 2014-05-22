<?php
namespace backend\controllers;

use Yii;
use backend\models\Seo;
use backend\models\LinkAdmin;
use backend\models\FriendLink;
use backend\components\BackendController;

class SeoController extends BackendController{

    public function actionTdkAdmin(){
        $model = new Seo;
        return $this->render('index',['model'=>$model]);
    }


    public function actionCreate()
    {
        $model = new Seo;
        if($model->load($_POST)){
            if($model->validate() && $model->save()){
                return $this->redirect("/seo/tdk-admin");
            }
        }
        return $this->render('create',['model'=>$model]);
    }
    

    public function actionUpdate($id){
        $post = Seo::findOne($id);
        if (!$post) {
            throw new NotFoundHttpException();
        }
        if($post->updateTdk($id)){
            return $this->redirect("/seo/tdk-admin");
        }
        return $this->render('create',['model'=>$post]);
    }

    public function actionLinkAdmin(){
        $model = new LinkAdmin;
        if($model->load($_POST)){
            if($model->validate() && $model->save()){
                return $this->redirect("/seo/link-admin");
            }
        }
        return $this->render("link_admin",['model'=>$model]);
    }


    public function actionUpdatelinkblock($id){
        $post = LinkAdmin::findOne($id);
        if (!$post) {
            throw new NotFoundHttpException();
        }
        if($post->updateLinkBlock($id)){
            return $this->redirect("/seo/link-admin");
        }
        return $this->render("link_admin",['model'=>$post]);
    }

    public function actionDelete($id){
        if(!empty($id)){
            $model = new LinkAdmin;
            if($model->deleteLinkBlock($id)){
                return $this->redirect("/seo/link-admin");
            }
        }
        return $this->redirect("/seo/link-admin");
    }



    public function actionAddlink($id){
        $model = new FriendLink;
        if(!empty($_POST)){
            if($model->addLinks($_POST)){
                return $this->redirect(["addlink",'id'=>$id]);
            }
        }
        return $this->render("friendlink",['model'=>$model,'block_id'=>$id]);
    }


    public function actionUpdateLink($id){
        $post = FriendLink::findOne($id);
        if (!$post) {
            throw new NotFoundHttpException();
        }
        if($post->updateFriendLink($id)){
            return $this->redirect(["addlink",'id'=>$post->link_type]);
        }
        return $this->render('friendlink', ['model' => $post,'isNew'=>false,'block_id'=>$post->link_type]);
    }


    public function actionDeleteLink($id){
        $model = FriendLink::findOne($id);
        $model->delete();
        return $this->redirect(["addlink",'id'=>$model->link_type]);
    }
}
