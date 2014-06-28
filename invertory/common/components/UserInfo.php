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
    	return Order::find()->where(['is_del'=>Order::ORDER_IS_NOT_DEL,'status'=>Order::NEW_ORDER])->count();
    }
    function getTotalorder(){
    	return Order::find()->where(['is_del'=>Order::ORDER_IS_NOT_DEL])->count();
    }
    function getRefuseorder(){
    	return Order::find()->where(['is_del'=>Order::ORDER_IS_NOT_DEL,'status'=>Order::REFUSE_ORDER])->count();
    }
    function getMyorder(){
    	return Order::find()->where(['is_del'=>Order::ORDER_IS_NOT_DEL,'created_uid'=>$this->_uid])->count();
    }

}