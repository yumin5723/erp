<?php

namespace gcommon\cms\controllers;

use Yii;
use gcommon\cms\models\Page;
use gcommon\cms\models\search\PageSearch;
use gcommon\cms\models\PageTerm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use gcommon\components\GController;
use gcommon\cms\components\CmsTasks;

class PageController extends GController {
    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex() {
        // renders the view file 'protected/views/site/index.php'
        // using the default layout 'protected/views/layouts/main.php'
        $this->render('index');
    }
    /**
     * This is the action to handle external exceptions.
     */
    public function actionError() {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest) echo $error['message'];
            else $this->render('error', $error);
        }
    }
    /**
     * Displays the page list
     */
    public function actionAdmin() {
        $searchModel = new PageSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('admin', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
    /**
     * Displays the create page
     */
    public function actionCreate() {
        $model = new Page;
        $page_status = $model->getPageStatus();
        $domains = $model->getCanUseDomain();
        // collect user input data
        if (isset($_POST['Page'])) {
            $model->load($_POST);
            if ($model->validate()) {
                if ($model->saveRarFile($_FILES)) {
                    $model->admin_id = Yii::$app->user->id;
                    $model->save();
                    //save page term sign page type for admin menu
                    // if($_POST['Page']['pagetype'] != "none"){
                    //     PageTerm::model()->signPageType($model->id,$_POST['Page']['pagetype']);
                    // }

                    (new CmsTasks())->parsePage($model->id);
                    Yii::$app->session->setFlash('success', '新建成功！');
                    $this->redirect("/cms/page/admin");
                }
            }
        }
        return $this->render('create', array(
            'model' => $model,"domains"=>$domains,'isNew'=>true,
        )); 
    }
    /**
     * Displays the create page
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        $domains = $model->getCanUseDomain();
        $templetes = Yii::$app->publisher->domains;
        // $model->pagetype = PageTerm::model()->getTypeByPageId($id);
        // collect user input data
        if (isset($_POST['Page'])) {
            $model->load($_POST);
            if(!empty($_FILES['Page']['name']['upload'])){
                $model->saveRarFile($_FILES);
            }else{
                $model->content = $_POST['Page']['content'];
            }
            $model->modified_id = Yii::$app->user->id;
            $model->status = Page::STATUS_DRAFT;
            $model->save(false);
            if(!empty($_FILES['Page']['name']['upload'])){
                (new CmsTasks())->parsePage($model->id);
            }
            // PageTerm::model()->updatePageType($model->id,$_POST['Page']['pagetype']);
            Yii::$app->session->setFlash('success', '修改成功！');
            $this->redirect("/cms/page/admin");
        }
        return $this->render('update', array(
            'model' => $model,"domains"=>$domains,"isNew"=>false
        ));
    }
    public function actionDelete($id){
        GxcHelpers::deleteModel('Page', $id);  
    }
    /**
     * push rar file
     */
    public function actionPublish($id) {
        $page = $this->loadModel($id);
        if($page->doPublish()){
            Yii::$app->session->setFlash('success', '发布成功！');
        }else{
            Yii::$app->session->setFlash('success', '发布失败请重新尝试!');
        }
        $this->redirect("/cms/page/admin");
    }
    /**
     * preview page for admin user
     */
    public function actionPreview($id){
        $page = $this->loadModel($id);
        echo $page->display();
    }
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = Page::findOne($id);
        if ($model === null) throw new CHttpException(404, 'The requested page does not exist.');
        
        return $model;
    }
}
