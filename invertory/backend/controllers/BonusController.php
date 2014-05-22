<?php
namespace backend\controllers;

use Yii;
use backend\models\BonusBack;
use backend\components\BackendController;

class BonusController extends BackendController{

    public function actionIndex(){
        $model = new BonusBack;
        return $this->render('index',['model'=>$model]);
    }


    public function actionCreate(){
        $model = new BonusBack;
        if(isset($_POST)){
            if($model->addBonus($_POST)){
                return $this->redirect("/bonus/index");
            }
        }
        return $this->render("create",['model'=>$model]);
    }


    public function actionUpdate(){
        $post = BonusBack::findOne($_GET['id']);
        if (!$post) {
            throw new NotFoundHttpException();
        }
        $post->start_date = date("Y-m-d",$post->start_date);
        $post->end_date = date("Y-m-d",$post->end_date);
        if($post->updateBonus($_GET['id'])){
            return $this->redirect("/bonus/index");
        }
        return $this->render('create', ['model' => $post]);
    }


    public function actionDelete(){
        if(isset($_GET['id'])){
            $id = intval($_GET['id']);
            $model = new BonusBack;
            if($model->deleteBonus($id)){
                return $this->redirect("/bonus/index");
            }
        }
        throw new NotFoundHttpException();
    }


    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = Bonus::findOne(['bonus_id'=>$id]);
        if ($model === null) { return false;}
        return $model;
    }
}