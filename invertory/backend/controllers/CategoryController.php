<?php
namespace backend\controllers;

use Yii;
use backend\models\CategoryBack;
use yii\data\ArrayDataProvider;
use backend\components\BackendController;

class CategoryController extends BackendController{

    /*
     * first level category
     */
    public function actionIndex(){
        $id = isset($_GET['id']) ? $_GET['id'] : "zTreeAsyncTest";
        $model = new CategoryBack; 
        return $this->render('index',array("model"=>$model,"id"=>$id));
    }
    /**
     * show root descendants
     */
    public function actionShow(){
        $root = $_GET["root"];
        $model = new CategoryBack;
        $root = $model->findOne(["root"=>$root]);
        return $this->render("show",array("root"=>$root,'model'=>$model));
    }
    /**
     * create category
     */
    public function actionCreate(){
        $root = $_GET['root'];
        $model = new CategoryBack;
        $roots = $model->findOne(["root"=>$root]);
        if(isset($_POST['CategoryBack'])){
            $model->setAttributes($_POST['CategoryBack']);
            $result = $model->setCategory($_POST['CategoryBack']['id'],Yii::$app->user->id);
            if($result[0] == true){
                return $this->redirect('/category/show?root='.$root);
            }
        }
        return $this->render('create',array('model'=>$model,'root'=>$roots,'category'=>$model->getCatIds()));
    }


    public function actionUpdate(){
        $id = $_GET['id'];
        $model = CategoryBack::findOne($id);
        $parent=$model->parent()->one();
        if(isset($_POST) && !empty($_POST)){
            $model->setAttributes($_POST['CategoryBack']);
            $result = $model->changeCategory($id,Yii::$app->user->id);
            if($_POST['CategoryBack']['id']){
                $first = CategoryBack::findOne($_POST['CategoryBack']['id']);
                $model->moveAsFirst($first);
            }
            return $this->redirect("/category/show?root=0");
        }
        return  $this->render("update",array("model"=>$model,'parent'=>$parent,'category'=>CategoryBack::getCatIds()));
    }


    /**
     * create root
     */
    public function actionRoot(){
        $model = new CategoryBack;
        if(isset($_POST['CategoryBack'])){
            $model->saveRootNode($_POST['CategoryBack']);
            $this->redirect("/category/index");
        }
        return $this->render("root",array('model'=>$model));
    }
    
    /**
     * delete category
     */
    public function actionDelete($id){
        $node = CategoryBack::findOne($id);
        $node->deleteNode();
        return $this->redirect(Yii::$app->request->getReferrer());
    }
    
}