<?php
namespace backend\controllers;

use backend\models\OrderBack;
use backend\models\OrderInfoBack;
use Yii;
use yii\web\Controller;
use backend\assets\AppAsset;

class OrderController extends Controller{

	public $sidebars = [
        [
            'name' => '订单列表',
            'icon' => 'tasks',
            'url' => 'index',
        ],
    ];
	public function behaviors()
	{
		return [
			'access' => [
				'class' => \yii\filters\AccessControl::className(),
				'only' => ['index', 'update','delete','check'],
				'rules' => [
					[
						'actions' => ['index','update','delete','check'],
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
		];
	}

    /**
     * 加载订单
     * @return string
     */
    public function actionIndex(){
		$model = new OrderBack();
		return $this->render('index',['model'=>$model]);
	}


    /**
     * 修改订单操作
     */
    public function actionUpdate()
    {
        $model = new OrderBack;
        if(!empty($_POST)){
            $result = $model->updateOrder();
            if($result){
                echo 1;
            }else{
                echo 2;
            }
            die;
        }
        return $this->render('update',['model' => $model->findOrder()]);
    }

    /**
     *
     */
    public function actionView(){

    }


    /**
     * 查看订单详情
     */
    public function actionViewdetail()
    {
        $model = new OrderInfoBack();
        return $this->render('orderInfo',['model' => $model]);
    }


    /**
     * 修改订单状态
     */
    public function actionChangeorderstatus()
    {
        $model = new OrderBack();
        /*$order =  OrderBack :: find(1);
        var_dump($order->getOrderInfo());
        die;*/
        //echo "<script>alert('OK')</script>";
        $result = $model->changeOrderStatus();
    }



}