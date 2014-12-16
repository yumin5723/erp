<?php

namespace gcommon\cms\controllers;

use Yii;
use gcommon\cms\models\Object;
use gcommon\cms\models\Template;
use gcommon\cms\models\Oterm;
use gcommon\cms\models\search\ObjectSearch;
use gcommon\cms\models\ObjectTemplate;
use gcommon\cms\models\ObjectTerm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use gcommon\components\GController;
use gcommon\cms\components\CmsTasks;
use gcommon\cms\components\ConstantDefine;
use yii\helpers\BaseArrayHelper;
use backend\models\Upload;
use yii\web\HttpException;

class ObjectController extends GController {
    public $enableCsrfValidation;
    /**
     * The function that do Create new Object
     *
     */
    public function actionCreate() {
        $model = new Object;
        $model->object_date = date( "Y-m-d H:i:s" );
        $content_status = $model->getContentStatus();
        $templetes = $model->getTemplates();
        $roots = Oterm::getCatIds();
        $selected_terms = array();
        if ( isset( $_POST["Object"] ) ) {
            $model->load($_POST);
            if($model->validate()){
                $model->object_status = ConstantDefine::OBJECT_STATUS_DRAFT;
                $model->object_date_gmt = date("Y-m-d H:i:s");
                //count page num
                $contents = array_filter(explode('[page]', $model->object_content));
                if(count($contents) > 1){
                    $model->page = count($contents);
                }
                $contents = array_filter(explode('[page]', $model->object_content));
                if ( $model->save() && $model->saveTemplate()) {
                    $objectterm = new ObjectTerm;
                    $objectterm->saveObjectTerm($model->object_id,$_POST['Oterm']);

                    if(isset($_POST['ispublish']) && $_POST['ispublish'] == 1){
                        $model->doPublish();
                    }

                    Yii::$app->session->setFlash( 'success', 'Create new Content Successfully!');
                    $this->redirect("/cms/object/list");
                } else {
                    $model->object_date=date( 'Y-m-d H:i:s', $model->object_date );
                }
            }
        }
        return $this->render( 'create', array( "model"=>$model, "content_status"=>$content_status,
             'templetes'=>$templetes,"isNew"=>true,"templete"=>"",'roots'=>$roots,"selected_terms"=>$selected_terms
            ) );
    }

    
    /**
     * The function that do Update Object
     *
     */
    public function actionUpdate() {
        $id = isset( $_GET['id'] ) ? (int)( $_GET['id'] ) : 0;
        $model=  $this->loadModel($id);
        $guid = $model->guid;
        $model->isNewRecord = false;
        $templates = $model->getTemplates();
        $templateModel = new ObjectTemplate;
        $template = $templateModel->findOne(['object_id'=>$id]);
        if(!empty($template)){
            $model->template_id = $template->templete_id;
        }
        $roots = Oterm::getCatIds();
        $select_terms = ObjectTerm::find()->where(["object_id"=>$model->object_id])->all();
        $select_terms = BaseArrayHelper::toArray($select_terms);
        if (isset( $_POST["Object"] ) ) {
            $model->load($_POST);
            if($model->validate()){
                //count page num
                $contents = array_filter(explode('[page]', $model->object_content));
                if(count($contents) > 1){
                    $model->page = count($contents);
                }
                $model->object_status = ConstantDefine::OBJECT_STATUS_DRAFT;
                if ( $model->save() ) {
                    if(!empty($template)){
                        $template->templete_id = isset($_POST['Object']['template_id']) ? $_POST['Object']['template_id'] : "0";
                        $template->save(false);
                    }else{
                        $templateModel->templete_id = isset($_POST['Object']['template_id']) ? $_POST['Object']['template_id'] : "0";
                        $templateModel->object_id = $model->id;
                        $templateModel->save();
                    }
                    if(isset($_POST['ispublish']) && $_POST['ispublish'] == 1){
                        $model->doPublish();
                    }
                    
                    Yii::$app->session->setFlash( 'success', '修改成功！' );
                    if($model->updateObjectTermCache()){
                        $objecttermModel = new ObjectTerm;
                        $objecttermModel->updateObjectTerm($model->object_id,$_POST['Oterm']);
                    }
                    //save resource
                    // $this->redirect("/cms/object/list");
                }
            }
        }
        return $this->render( 'create',array("model"=>$model,
            'templetes'=>$templates,"isNew"=>false,"roots"=>$roots,"selected_terms"=>$select_terms
            ) );
    }
    /**
     * The function that do Manage Object
     *
     */
    public function actionList() {
        $searchModel = new ObjectSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('admin', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
    /**
     * This function sugget Person that the current user can send content to
     *
     */
    public function actionSuggestPeople() {
        $this->widget( 'cmswidgets.object.ObjectExtraWorkWidget', array(
                'type' => 'suggest_people'
            ) );
    }
    /**
     * This function sugget Person that the current user can send content to
     *
     */
    public function actionCheckTransferRights() {
        $this->widget( 'cmswidgets.object.ObjectExtraWorkWidget', array(
                'type' => 'check_transfer_rights'
            ) );
    }
    /**
     * This function sugget Tags for Object
     *
     */
    public function actionSuggestTags() {
        $this->widget( 'cmswidgets.object.ObjectExtraWorkWidget', array(
                'type' => 'suggest_tags'
            ) );
    }
    /**
     * The function is to Delete a Content
     *
     */
    public function actionDelete( $id ) {
       $model = new Object;
        $result = $model->updateByPk((int)$id,array('object_status'=>ConstantDefine::OBJECT_STATUS_DELETE));
        if($result !== false){
            $object = $model->findByPk($id);
            $updateTemplate = $object->doDelete();
            $this->redirect("/cms/object/admin/type/0");
        }
    }
    /**
     * preview object
     */
    public function actionPreview($id){
        $object = Object::findOne($id);
        echo $object->display(1,$object->page);
    }
    /**
     * publish content as html
     */
    public function actionPublish($id){
        $object = Object::findOne($id);
        $result = $object->doPublish();
        if($result !== false){
            // Yii::app()->user->setFlash( 'success', Yii::t( 'cms', '发布成功!' ) );
            $this->redirect(Yii::$app->request->referrer);
        }
    }
    /**
     * the action is get object by category id
     */
    public function actionFind($id){
        $model = new Object;
        $model->unsetAttributes(); 
        $result = ObjectTerm::model()->fetchObjectsByTermid($id);
        $term = Term::model()->findByPk($id);
        $this->render( 'find', array(
            'model'=>$model,
            'result'=>$result,
            'term'=>$term,
            ) );
    }
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = Object::findOne($id);
        if ($model === null) throw new HttpException(404, 'The requested page does not exist.');
        
        return $model;
    }
    /**
     * upload image from ckeditor
     * @return [type] [description]
     */
    public function actionUpload(){
        $this->enableCsrfValidation = false;
        $fn=$_GET['CKEditorFuncNum'];

        if($_FILES){
            $model = new Upload;
            $result = $model->uploadImage($_FILES);
            if($result[0] == true){
                $message = "上传成功";
                $fileurl = $result[1];
            }else{
                $fileurl = "";
                $message = $result[1];
            }
            $str='<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction('.$fn.', \''.$fileurl.'\', \''.$message.'\');</script>';
            exit($str);
        }
    }
     
}
