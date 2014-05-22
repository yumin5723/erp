<?php
namespace common\components;
use Yii;
use common\models\User;
use yii\db\Query;
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
    /*
     *  return nickname
     */
    function getNickname(){
    	$nickname = $this->getUser()->nickname;
    	return $nickname;
    }
    function getDefaultdog(){
        $dogUcenterDb = Yii::$app->get('dogucenterdb');

        $query = new Query;
        $result = $query->select('user_dog_id')
              ->from('nd_user')
              ->where(['user_id'=>Yii::$app->user->id])
              ->one($dogUcenterDb);
        if(!empty($result)){
            $dog_id = $result['user_dog_id'];
            $dogspe = $query->select('t.spe_name_s')
                            ->from('dog_species t,dog_doginfo d')
                            ->where("d.dog_species=t.spe_id AND d.dog_id=$dog_id")
                            ->one(\Yii::$app->get('dogdb'));

            $d = explode("/", $dogspe['spe_name_s']);
            return $d[0];
        }
        return "";
    }
    /**
     * get data from www api
     * @return [type] [description]
     */
    function getUnreadmsg(){
        $api = "http://www.goumin.com/api/getMsgcount.php?uid=".Yii::$app->user->id;
        $data = file_get_contents($api);
        $data = json_decode($data,true);
        return $data;
    }
    function getAge(){
        $dogUcenterDb = Yii::$app->get('dogucenterdb');

        $query = new Query;
        $result = $query->select('user_dog_id')
              ->from('nd_user')
              ->where(['user_id'=>Yii::$app->user->id])
              ->one($dogUcenterDb);
        if(!empty($result)){
          $dog_id = $result['user_dog_id'];
          $result = $query->select('dog_birth_y,dog_birth_m,dog_birth_d')
                ->from('dog_doginfo')
                ->where(['dog_id'=>$dog_id])
                ->one(\Yii::$app->get('dogdb'));
          if(!empty($result)){
             if($result['dog_birth_y'] == ""){
                return "";
             }
             $year = $result['dog_birth_y'];
             if($result['dog_birth_m'] == ""){
                $month = "00";
             }else{
                $month = $result['dog_birth_m'];
             } 
             $year = $year."-".$month."-01";
             $months =(time() - strtotime($year))/2592000;
             $year = floor($months/12);
             $month = floor($months - $year*12);
             return $year == "0" ? $month."月" : $year."年".$month."月";
          }
        }else{
          return "";
        }
        // $age = ((time() - $result['dog_birth_time']) / 2592000)/12;

    }

}