<?php
namespace backend\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use backend\models\ExpressBack;
use backend\models\ExpressArea;
use backend\models\ExpressInfo;
use backend\components\BackendController;


class ExpressController extends BackendController{


    public function actionIndex(){
        $model = new ExpressBack;
        return $this->render("index",['model'=>$model]);
    }

    public function actionCreate(){
        $model = new ExpressBack;
        $model->loadDefaultValues();
        if(isset($_POST) && !empty($_POST)){
            if($model->addExpress($_POST)){
                return $this->redirect("/express/index");
            }
        }
        return $this->render("create",['model'=>$model]);
    }

    public function actionUpdate(){
        $post = ExpressBack::findOne($_GET['id']);
        if (!$post) {
            throw new NotFoundHttpException();
        }
        if($post->updateExpress($_GET['id'])){
            return $this->redirect("/express/index");
        }
        return $this->render('create',['model'=>$post]);
    }

    public function actionDelete(){
        $model = new ExpressBack;
        $id = intval($_GET['id']);
        $model->deleteExpress($id);
        return $this->redirect("/express/index");
    }

    public function actionView(){
        $id = intval($_GET['id']);
        $model = new ExpressBack;
        $rs = $model->findExpress($id);
        return $this->render("view",['model'=>$rs]);
    }

    public function actionAdetail(){
        if(isset($_GET['id'])){
            $model = new ExpressArea;
            $model->loadDefaultValues();
            $rs = $model->getAllCity();
            if(!empty($_POST)){
                $result = $model->addExpArea($_POST);
                if($result){
                    return  '<script>alert("'.$result.'已经被设置，不允许重复设置！"); history.back();</script>';
                }else{
                    return $this->redirect(['adetail','id'=>$_GET['id']]);
                }        
            }
            return $this->render("create_express_info",['model'=>$model,'shipping_id'=>$_GET['id'],'areas'=>$rs]);
        }
        throw new NotFoundHttpException();
    }

    public function actionUpdetail(){
        $post = ExpressArea::findOne($_GET['id']);
        // print_r($post);exit;
        if (!$post) {
            throw new NotFoundHttpException();
        }
        if($post->updateExpDetail($_GET['id'])){
            return $this->redirect(["adetail",'id'=>$post->shipping_id]);
        }
        return $this->render('create_express_info', ['model' => $post,'shipping_id'=>$post->shipping_id,'areas'=>$post->getAllCity(),'cityNames'=>$post->showCity($post->area_id),'area_id'=>$post->area_id]);
    }

    public function actionDeldetail(){
        if(isset($_GET['id'])){
            $model = new ExpressArea;
            if($model->deleteExpDetail($_GET['id'])){
                return $this->redirect(Yii::$app->request->getReferrer());
            }
        }
        return $this->redirect(Yii::$app->request->getReferrer());
    }
    //change express tpl status
    public function actionChange(){
        if(!empty($_GET['id']) && !empty($_GET['act'])){
            $model = new ExpressBack;
            $model->change($_GET['id'],$_GET['act']);
        }
        return $this->redirect("/express/index");
    }
}