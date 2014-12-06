<?php
namespace common\models;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Security;
use yii\web\IdentityInterface;
use gcommon\cms\models\editor\DogNewthings;
use yii\db\Query;
/**
 * Class User
 * @package common\models
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $role
 * @property integer $status
 * @property integer $create_time
 * @property integer $update_time
 */
class User extends ActiveRecord implements IdentityInterface
{

	private $_user = false;
	public $rememberMe = true;
	public $auth_key;
	const AUTH_SEED = "DOG123loveme";
	const COOKIE_NAME_AUTH = 'dog_auth';
	// /**
	//  * @var string the raw password. Used to collect password input and isn't saved in database
	//  */
	// public $password;

	// const STATUS_DELETED = 0;
	// const STATUS_ACTIVE = 10;

	// const ROLE_USER = 10;

	// public function behaviors()
	// {
	// 	return [
	// 		'timestamp' => [
	// 			'class' => 'yii\behaviors\AutoTimestamp',
	// 			'attributes' => [
	// 				ActiveRecord::EVENT_BEFORE_INSERT => ['create_time', 'update_time'],
	// 				ActiveRecord::EVENT_BEFORE_UPDATE => 'update_time',
	// 			],
	// 		],
	// 	];
	// }
    public static function tableName(){
        return "pre_ucenter_members";
    }
	// /**
	//  * Finds an identity by the given ID.
	//  *
	//  * @param string|integer $id the ID to be looked for
	//  * @return IdentityInterface|null the identity object that matches the given ID.
	//  */
	public static function findIdentity($id)
	{
		return static::findOne($id);
	}
 //   /**
	// * @inheritdoc
	// */
    public static function findIdentityByAccessToken($token)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }
	// // public static function findIdentityByAccessToken($token){}
	// /**
	//  * Finds user by username
	//  *
	//  * @param string $username
	//  * @return null|User
	//  */
	public static function findByUsername($username)
	{
		return static::findOne(['username' => $username]);
	}

	// /**
	//  * @return int|string|array current user ID
	//  */
	public function getId()
	{
		return $this->getPrimaryKey();
	}
	public function getName() {
        return $this->user->username;
    }
	// /**
	//  * @return string current user auth key
	//  */
	public function getAuthKey()
	{
		return $this->auth_key;
	}

	// /**
	//  * @param string $authKey
	//  * @return boolean if auth key is valid for current user
	//  */
	public function validateAuthKey($authKey)
	{
		return $this->getAuthKey() === $authKey;
	}

	// /**
	//  * @param string $password password to validate
	//  * @return bool if password provided is valid for current user
	//  */
	// public function validatePassword($password)
	// {
	// 	return Security::validatePassword($password, $this->password_hash);
	// }

	// public function rules()
	// {
	// 	return [
	// 		['status', 'default', 'value' => self::STATUS_ACTIVE],
	// 		['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],

	// 		['role', 'default', 'value' => self::ROLE_USER],
	// 		['role', 'in', 'range' => [self::ROLE_USER]],

	// 		['username', 'filter', 'filter' => 'trim'],
	// 		['username', 'required'],
	// 		['username', 'string', 'min' => 2, 'max' => 255],

	// 		['email', 'filter', 'filter' => 'trim'],
	// 		['email', 'required'],
	// 		['email', 'email'],
	// 		['email', 'unique', 'message' => 'This email address has already been taken.', 'on' => 'signup'],
	// 		['email', 'exist', 'message' => 'There is no user with such email.', 'on' => 'requestPasswordResetToken'],

	// 		['password', 'required'],
	// 		['password', 'string', 'min' => 6],
	// 	];
	// }

	// public function scenarios()
	// {
	// 	return [
	// 		'signup' => ['username', 'email', 'password', '!status', '!role'],
	// 		'resetPassword' => ['password'],
	// 		'requestPasswordResetToken' => ['email'],
	// 	];
	// }

	public function beforeSave($insert)
	{
		// if (parent::beforeSave($insert)) {
		// 	if (($this->isNewRecord || $this->getScenario() === 'resetPassword') && !empty($this->password)) {
		// 		$this->password_hash = Security::generatePasswordHash($this->password);
		// 	}
			// if ($this->isNewRecord) {
				$this->auth_key = Security::generateRandomString();
			// }
			return true;
		// }
		// return false;
	}

	/**
	 * Logs in a user using the provided username and password.
	 * @return boolean whether the user is logged in successfully
	 */
	public function login()
	{
		// if ($this->validate()) {
		// 	return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
		// } else {
		// 	return false;
		// }
		return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
	}

	/**
	 * Finds user by [[username]]
	 *
	 * @return User|null
	 */
	private function getUser()
	{
		if ($this->_user === false) {
			$this->_user = User::findByUsername($this->username);
		}
		return $this->_user;
	}
	public static function getDb(){
		return \Yii::$app->get("dogdb");
	}
	/**
	 * [getDogSpeIdByUser description]
	 * @return [type] [description]
	 */
	public static function  getDogSpeIdByUser($uid){
        $dogUcenterDb = Yii::$app->get('dogucenterdb');

        $query = new Query;
        $result = $query->select('user_dog_id')
              ->from('nd_user')
              ->where(['user_id'=>$uid])
              ->one($dogUcenterDb);
        if(!empty($result)){
	        $dog_id = $result['user_dog_id'];
	        $dogspe = $query->select('t.spe_name_s,spe_id')
	                        ->from('dog_species t,dog_doginfo d')
	                        ->where("d.dog_species=t.spe_id AND d.dog_id=$dog_id")
	                        ->one(\Yii::$app->get('dogdb'));
	        return $dogspe['spe_id'];
        }
        return "";
	}
	/**
	 * [getDogNewThingsByUserId description]
	 * @param  [type] $uid [description]
	 * @return [type]      [description]
	 */
	public static function getDogNewThingsByUserId($uid){
		$spe_id = static::getDogSpeIdByUser($uid);
		$thingsModel = new DogNewThings;
		$result = $thingsModel->getPostsBySpeId($spe_id);
		return $result; 
	}
	/**
	 * check user is login
	 */
	public static function checkUserLogin(){
		$request = new \yii\web\Request;
		$request->enableCookieValidation = false;
		$user_id = $request->cookies->getValue("dog_uid");
		$user_name = $request->cookies->getValue("dog_user");
		$exp = $request->cookies->getValue("dog_exp");
		$auth = sha1($user_id.$user_name.$exp.self::AUTH_SEED);

		$cookie_auth = $request->cookies->getValue(self::COOKIE_NAME_AUTH);
		if($auth != $cookie_auth){
			Yii::$app->user->logout();
			return false;
		}
		return $user_id;
	}
	/**
	 * [getKnowByDog description]
	 * @param  [type] $dog [description]
	 * @return [type]      [description]
	 */
	public static function getKnowByDog($user_id){
		$dog = self::getDefaultdog($user_id);
		$query = new Query;
        $results = $query
            ->select('qst_id, qst_subject')
            ->from('dog_ask_question')
            ->where("qst_hide = 0")
            ->andWhere("status = 1")
            ->andWhere("auth=0")
            ->andWhere("qst_ansnum>0")
            ->andFilterWhere(['like', 'qst_subject', $dog]) 
            ->orderBy('qst_id desc')
            ->limit(2)
            ->all(self::getDb());
        return $results;
	}
	public static function getDefaultdog($user_id){
        $dogUcenterDb = Yii::$app->get('dogucenterdb');

        $query = new Query;
        $result = $query->select('user_dog_id')
              ->from('nd_user')
              ->where(['user_id'=>$user_id])
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
    public static function getUserRequestVersion(){
    	$request = new \yii\web\Request;
		$request->enableCookieValidation = false;
		$version = $request->cookies->getValue("gmversion");
		return $version;
    }
}
