<?php
namespace backend\controllers;

use Yii;
use backend\models\Keyword;
use backend\components\BackendController;

class KeywordController extends BackendController{

    public function actionList(){
        $model = new Keyword;
        $data = $model->getKeyWordList();
        return $this->render('keys',['data'=>$data,'count'=>count($data)]);
    }

    public function actionDelete(){
        $word = isset($_GET["word"]) && !empty($_GET["word"]) ? $_GET["word"] : "";
        if ($word != "") {
            $model = new Keyword;
            $model->delKeyword($word);
            $this->redirect("keyword/list");
        }
    }

    public function actionCreate(){
        if (isset($_POST["keyword"]) && !empty($_POST["keyword"])) {
            $model = new Keyword;
            $model->createNewKeyword(trim($_POST["keyword"]));
            $this->redirect("keyword/list");
        }
        return $this->render("create");
    }

}
