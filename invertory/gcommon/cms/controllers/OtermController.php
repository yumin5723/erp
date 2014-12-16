<?php
namespace gcommon\cms\controllers;

use Yii;
use gcommon\cms\models\Oterm;
use gcommon\cms\models\search\ObjectSearch;
use gcommon\cms\models\ObjectTerm;
use yii\data\ArrayDataProvider;
use gcommon\components\GController;
class OtermController extends GController {
       /*
     * first level category
     */
    public function actionIndex(){
        $id = isset($_GET['id']) ? $_GET['id'] : "zTreeAsyncTest";
        $model = new Oterm;
        return $this->render('index',array("model"=>$model,"id"=>$id));
    }
    /**
     * show root descendants
     */
    public function actionShow(){
        $root = $_GET["root"];
        $model = new Oterm;
        $root = $model->findOne(["root"=>$root]);
        return $this->render("show",array("root"=>$root,'model'=>$model));
    }
    /**
     * create category
     */
    public function actionCreate(){
        $root = $_GET['root'];
        $model = new Oterm;
        $roots = $model->findOne(["root"=>$root]);
        $templates = $model->getTemplates();
        if(isset($_POST['Oterm'])){
            $model->setAttributes($_POST['Oterm']);
            $result = $model->setCategory($_POST['Oterm']['id'],Yii::$app->user->id);
            if($result[0] == true){
                return $this->redirect('/cms/oterm/show?root='.$root);
            }
        }
        return $this->render('create',array('model'=>$model,'root'=>$roots,'templates'=>$templates,'category'=>$model->getCatIds()));
    }


    public function actionUpdate(){
        $id = $_GET['id'];
        $model = Oterm::findOne($id);
        $templates = $model->getTemplates();
        $parent=$model->parent()->one();
        if(isset($_POST) && !empty($_POST)){
            $model->setAttributes($_POST['Oterm']);
            $result = $model->changeCategory($id,Yii::$app->user->id);
            if($_POST['Oterm']['id']){
                $first = Oterm::findOne($_POST['Oterm']['id']);
                $model->moveAsFirst($first);
            }
            return $this->redirect("/cms/oterm/show?root=0");
        }
        return  $this->render("update",array("model"=>$model,'parent'=>$parent,'templates'=>$templates,'category'=>Oterm::getCatIds()));
    }

    /**
     * view this term object 
     */
    public function actionView($id){
        $model = new ObjectTerm;
        $result = $model->fetchObjectsByTermid1($id);
        $searchModel = new ObjectSearch;
        return $this->render( 'view', array(
            'result'=>$result,
            'searchModel'=>$searchModel,
            ) );
    }
    /**
     * create root
     */
    public function actionRoot(){
        $model = new Oterm;
        if(isset($_POST['Oterm'])){
            $model->saveRootNode($_POST['Oterm']);
            $this->redirect("/cms/oterm/index");
        }
        return $this->render("root",array('model'=>$model));
    }
    
    /**
     * delete category
     */
    public function actionDelete($id){
        $node = Oterm::findOne($id);
        $node->deleteNode();
        return $this->redirect(Yii::$app->request->getReferrer());
    }
    /**
     * preview object
     */
    public function actionPreview($id){
        $category = Oterm::findOne($id);
        echo $category->display(1);
    }
    /**
     * publish content as html
     */
    public function actionPublish($id){
        $category = Oterm::findOne($id);
        if(empty($category) || $category->template_id == 0){
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        $result = $category->doPublish();
        if($result !== false){
            Yii::$app->session->setFlash( 'success','cms', '发布成功!' );
            return $this->redirect("/cms/oterm/show?root=".$category->root);
        }
    }
}