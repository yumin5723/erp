<?php
namespace backend\Controllers;

use Yii;
use yii\web\Controller;
use backend\models\Upload;

class UploadController extends Controller{

    public $layout = "bak";
    public function actionIndex(){
        // $model = new Upload;
        // print_r($_POST);exit;
        // if(isset($_FILES['image'])){
        //     $status = $model->uploadImage($_FILES['image']);
        // }
        return $this->render('index');
    }
}