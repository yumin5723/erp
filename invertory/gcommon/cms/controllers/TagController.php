<?php
namespace gcommon\cms\controllers;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use gcommon\components\GController;
use gcommon\cms\models\Tag;
use gcommon\cms\models\search\TagSearch;
class TagController extends GController {
    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex() {
        // renders the view file 'protected/views/site/index.php'
        // using the default layout 'protected/views/layouts/main.php'
        return $this->render('index');
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
     * Lists all Block models.
     * @return mixed
     */
    public function actionList()
    {
        $searchModel = new TagSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('list', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
    /**
     * publish content as html
     */
    public function actionPublish($id){
        $tag = Tag::findOne($id);
        $result = $tag->doPublish();
        if($result !== false){
            // Yii::app()->user->setFlash( 'success', Yii::t( 'cms', '发布成功!' ) );
            $this->redirect("/cms/tag/list");
        }
    }
    /**
     * Displays the create page
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        // collect user input data
        if (isset($_POST['Tag'])) {
            $model->load($_POST);
            $position = strpos($_POST['Tag']['url'],"http:");
            if($position !== false){
                $model->frequency = 2;
            }
            if($model->save()){
                Yii::$app->session->setFlash('success', '修改成功！');
                $this->redirect("/cms/tag/update?id={$id}");
            }
        }
        return $this->render('create', array(
            'model' => $model
        ));
    }
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = Tag::findOne((int)$id);
        if ($model === null) throw new CHttpException(404, 'The requested page does not exist.');
        
        return $model;
    }
}
