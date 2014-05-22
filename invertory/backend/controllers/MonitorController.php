<?php
namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use backend\models\Monitor;
use yii\filters\VerbFilter;

class MonitorController extends Controller{
	public $sidebars = [
        [
            'name' => '管理监控点',
            'icon' => 'tasks',
            'url' => 'admin',
        ],
        [
            'name' => '创建监控点',
            'icon' => 'tasks',
            'url' => 'create',
        ],
    ];
    public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['admin', 'create','delete','update'],
				'rules' => [
					[
						'actions' => ['create','admin','delete','update'],
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
		];
	}

	public function actionAdmin()
	{
		$model = new Monitor;
		$data = $model->getAllData();
		return $this->render('admin',['data'=>$data]);
	}

	public function actionCreate()
	{
		$model = new Monitor;
		if($model->load($_POST) && $model->save()){
			return $this->redirect("/monitor/admin");
		}
		return $this->render('create',['model'=>$model]);
	}
	public function actionUpdate(){
		$model = new Monitor;
		$id = $_GET['id'];
		if($id){
			$model = $this->loadModel($id);
			if (!empty($_POST)) {
                if ($model->updateAttrs($_POST['Monitor'])) {
                    return $this->redirect("/monitor/admin");
                }
            }
		}
		return $this->render('create',['model'=>$model]);
	}

	/**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = Monitor::find(['id'=>$id]);
        if ($model === null) { return false;}
        return $model;
    }
}