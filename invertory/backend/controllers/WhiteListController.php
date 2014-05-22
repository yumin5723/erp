<?php
namespace backend\controllers;

use Yii;
use backend\models\WhiteList;
use backend\components\BackendController;

class WhiteListController extends BackendController{

    public function actionAdmin(){
        $model = new WhiteList;
        return $this->render('index',['model'=>$model]);
    }


    public function actionCreate()
    {
        $model = new WhiteList;
        if($model->load($_POST)){
            if($model->validate() && $model->save()){
                return $this->redirect("/white-list/index");
            }
        }
        return $this->render('index',['model'=>$model]);
    }
    

    public function actionUpdate($id){
        $post = WhiteList::findOne($id);
        if (!$post) {
            throw new NotFoundHttpException();
        }
        if($post->updateTdk($id)){
            return $this->redirect("/white-list/index");
        }
        return $this->render('index',['model'=>$post]);
    }

    public function actionDelete($id){
        $model = WhiteList::findOne($id);
        $model->delete();
        return $this->redirect("/white-list/index");
    }
}
