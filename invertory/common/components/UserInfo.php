<?php
namespace common\components;
use Yii;
use common\models\User;
use yii\db\Query;
use backend\models\Order;
class UserInfo{
	protected $_uid;
	protected $_user;
	protected $_profile;

	public static function factory($uid){
		$info = new self;
		if(is_numeric($uid)){
			$info->_uid = $uid;
		}elseif($uid instanceof User){
			$info->_uid = $uid->id;
			$info->_user = $uid;
		}
		return $info;
	}
	public function getUser(){
		if(is_null($this->_user)){
			$this->_user = User::findOne($this->_uid);
		}
		return $this->_user;
	}
	public function __get($name){
		return $this->getUser()->$name;
	}

	public function __isset($name){
		return isset($this->_user->$name);
	}

	public function __unset($name){
		$this->_user->__unset($name);
	}

	public function __call($name, $parameters) {
        if (method_exists($this->getProfile(), $name)) {
            return call_user_func_array(array($this->getProfile(), $name), $parameters);
        }
        if (method_exists($this->getUser(), $name)) {
            return call_user_func_array(array($this->getUser(),$name), $parameters);
        }
        throw new CException(Yii::t('yii','{class} does not have a method named "{name}".',array('{class}'=>get_class($this), '{name}'=>$name)));
    }
    /*
     *  return username
     */
    function getUsername() {
          $username = empty($this->getUser()->username) ?  $this->getUser()->email : $this->getUser()->username;
          return $username;
    }
    /*
     *  return email
     */
    function getUserEmail() {
        if ($email = $this->getUser()->email) {
            return $email;
        }
    }
    function getUnoptorder(){
        if(\Yii::$app->user->identity->storeroom_id == Order::BIGEST_STOREROOM_ID){
            return Order::find()->where(['is_del'=>Order::ORDER_IS_NOT_DEL,'status'=>Order::NEW_ORDER])->count();
        }
        return Order::find()->where(['is_del'=>Order::ORDER_IS_NOT_DEL,'status'=>Order::NEW_ORDER,'storeroom_id'=>\Yii::$app->user->identity->storeroom_id])->count();
    	
    }
    function getTotalorder(){
        if(\Yii::$app->user->identity->storeroom_id == Order::BIGEST_STOREROOM_ID){
            return Order::find()->where(['is_del'=>Order::ORDER_IS_NOT_DEL])->count();
        }
    	return Order::find()->where(['is_del'=>Order::ORDER_IS_NOT_DEL,'storeroom_id'=>\Yii::$app->user->identity->storeroom_id])->count();
    }
    function getRefuseorder(){
    	return Order::find()->where(['is_del'=>Order::ORDER_IS_NOT_DEL,'status'=>Order::REFUSE_ORDER])->count();
    }
    function getMyorder(){
    	return Order::find()->where(['is_del'=>Order::ORDER_IS_NOT_DEL,'created_uid'=>$this->_uid])->count();
    }
    function getMyrefuseorder(){
        return Order::find()->where(['is_del'=>Order::ORDER_IS_NOT_DEL,'created_uid'=>$this->_uid,'status'=>Order::REFUSE_ORDER])->count();
    }
    function getMyconfirmorder(){
        return Order::find()->where(['is_del'=>Order::ORDER_IS_NOT_DEL,'created_uid'=>$this->_uid,'status'=>Order::CONFIRM_ORDER])->count();
    }
    function getMypackageorder(){
        return Order::find()->where(['is_del'=>Order::ORDER_IS_NOT_DEL,'created_uid'=>$this->_uid,'status'=>Order::PACKAGE_ORDER])->count();
    }
    function getMyshippingorder(){
        return Order::find()->where(['is_del'=>Order::ORDER_IS_NOT_DEL,'created_uid'=>$this->_uid,'status'=>Order::SHIPPING_ORDER])->count();
    }
    function getMysignorder(){
        return Order::find()->where(['is_del'=>Order::ORDER_IS_NOT_DEL,'created_uid'=>$this->_uid,'status'=>Order::SIGN_ORDER])->count();
    }
    function getMyunsignorder(){
        return Order::find()->where(['is_del'=>Order::ORDER_IS_NOT_DEL,'created_uid'=>$this->_uid,'status'=>Order::UNSIGN_ORDER])->count();
    }

}