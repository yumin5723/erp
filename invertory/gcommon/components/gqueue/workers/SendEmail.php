<?php

namespace gcommon\components\gqueue\workers;
use Yii;
use backend\models\Order;
class SendEmail
{
    const QUEUE_NAME = "send_email";


    public function setUp()
    {
        # Set up environment for this job
    }

    public function perform()
    {
        $type = $this->args['type'];
        $id = $this->args['id'];
        $order = Order::findOne($id);
        if($type == Order::SIGN_ORDER){
            Yii::$app->mail->compose('confirm',['order'=>$order])
                         ->setFrom('liuwanglei2001@163.com')
                         ->setTo('liuwanglei@goumin.com')
                         ->setSubject("订单确认通知")
                         ->send();
        }
        
    }

    public function tearDown()
    {
        # Remove environment for this job
    }
}
