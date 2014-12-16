<?php
namespace gcommon\cms\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use backend\components\BackendController;
use gcommon\cms\models\editor\DogNewthings;
use gcommon\cms\models\editor\NewDogs;
use gcommon\cms\models\editor\NewPhotos;
use gcommon\cms\goumindata\Goumindata;

class EditorController extends BackendController{

	// public function actionDogIndex(){

	// 	$model = new DogNewthings;
	// 	return $this->render("dog_index",['model'=>$model]);
	// }

	public function actionDogIndex(){

		$model = new DogNewthings;
		return $this->render("dog_newthings",['model'=>$model]);
	}

	public function actionCreateNewthings(){
		$model = new DogNewthings;
		$species = $model->getSpecies();
		$msg = "";
		if(!empty($_POST)){
			$status = $model->createNewthings($_POST);
			if($status){
				return $this->redirect("/cms/editor/dog-index");
			}else{
				Yii::$app->session->setFlash('error', '每个犬种最多只能关联一个论坛版块,请勿重复添加或版块ID犬种!');
			}
		}
		return $this->render("create_newthings",['model'=>$model,'species'=>$species,'isNew'=>true]);
	}


	public function actionUpdate(){
		if(empty($_GET['id'])){
			throw new NotFoundHttpException();
		}
		$post = DogNewthings::findOne($_GET['id']);
		if(!$post){
			throw new NotFoundHttpException();
		}
		$status = $post->updateThing($_GET['id']);
		if($status===true){
			return $this->redirect("/cms/editor/dog-index");
		}elseif($status=='2'){
			Yii::$app->session->setFlash('warning', '每个犬种最多只能关联一个论坛版块,请勿重复添加或版块ID犬种!');
		}
		return $this->render("create_newthings",['model'=>$post,'species'=>$post->getSpecies(),'isNew'=>false]);
	}


	public function actionDelete(){
		if(empty($_GET['id'])){
			throw new NotFoundHttpException();
		}
		$model = new DogNewthings;
		$model->delThing($_GET['id']);
		return $this->redirect("/cms/editor/dog-index");
	}


	public function actionNewDog(){
		$model = new NewDogs;
		$beforedata = $model->getAllOldData();
		$afterdata = $model->getAllNewData();
		return $this->render("new_dog",['model'=>$model,'count'=>count($afterdata),'dogs'=>$beforedata,'new_dogs'=>$afterdata]);
	}


	public function actionPushDog(){
		$dog_id = $_GET['dog_id'];
		$model = new NewDogs;
		$model->pushData($dog_id);
		return $this->redirect("/cms/editor/new-dog");
	}


	public function actionCancelDog(){
		$dog_id = $_GET['dog_id'];
		$model = new NewDogs;
		$model->cancelData($dog_id);
		return $this->redirect("/cms/editor/new-dog");
	}

	public function actionNewPhoto(){
		$model = new NewPhotos;
		$beforedata = $model->getAllOldData();
		$afterdata = $model->getAllNewData();
		return $this->render("new_photo",['model'=>$model,'count'=>count($afterdata),'photos'=>$beforedata,'new_photos'=>$afterdata]);
	}

	public function actionPushPhoto(){
		$pht_id = $_GET['pht_id'];
		$model = new NewPhotos;
		$model->pushData($pht_id);
		return $this->redirect("/cms/editor/new-photo");
	}

	public function actionCancelPhoto(){
		$pht_id = $_GET['pht_id'];
		$model = new NewPhotos;
		$model->cancelData($pht_id);
		return $this->redirect("/cms/editor/new-photo");
	}
}