<?php

class User extends CActiveRecord
{
    public $password_repeat;
    public $rememberMe;

    public $verifyCode;
    public $reset_password_code;
    protected $reset_password_tablename = 'user_reset_pass';

    public $old_password;
    public $new_password;
    public $confirm_password;
    public $nickname;

    /**
     * table name
     *
     *
     * @return
     */
    public function tableName() {
        return 'user';
    }

    /**
     * Returns the static model of the specified AR class.
     * This method is required by all child classes of CActiveRecord.
     * @return CActiveRecord the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * set attribution validator rules
     *
     *
     * @return
     */
    public function rules() {
        $rules =  array(
            array('email,password,password_repeat,', 'required', 'on'=>'register',),
            array('email', 'length', 'max' => 40),
            array('password,old_password,new_password', 'length','min'=>6,'max'=>'128'),
            array('email', 'email'),
            array('email', 'unique', 'on'=>'register'),
            array('password', 'compare', 'compareAttribute'=>'password_repeat', 'on'=>'register'),
            array('email, password', 'required', 'on' => 'login'),
            array('password', 'authenticatePass', 'on' => 'login'),
            array('email', 'required', 'on'=>'recover'),
            array('password, reset_password_code','required','on'=>'resetPass'),
            array('old_password,new_password,confirm_password', 'required','on'=>'changePass'),
            array('old_password','check_password','on'=>'changePass'),
            array('new_password','compare','compareAttribute'=>'confirm_password', 'on'=>'changePass'),
            array('password','compare','compareAttribute'=>'confirm_password', 'on'=>'resetPass'),
            array('reset_password_code', 'resetPassword','on'=>'resetPass'),
            array('username', 'length', 'min'=>5, 'max'=>20, 'on'=>'update'),
            array('nickname', 'required','on'=>'setusername'),
            array('nickname', 'check_username','on'=>'setusername'),
            array('verifyCode','captcha','allowEmpty'=>YII_DEBUG,'on'=>"recover"),
        );
        /*
         * if (!isset(Yii::app()->params['needAlphaCode']) || !Yii::app()->params['needAlphaCode']) {
         *     $rules[] = array('verifyCode', 'captcha', 'allowEmpty'=>YII_DEBUG, 'on' => 'register');
         * }
         */
        return $rules;
    }
    public function safeAttributes() {
        return array(
            //parent::safeAttributes(),
            'login'=>'email, password, rememberMe',
        );
    }

    /**
     * hash password
     *
     *
     * @param username
     * @param password not hash yet
     *
     * @return
     */
    protected function hashPassword($password) {
        $mode = 'sha1';
        // hash the text //
        $textHash = hash($mode, $password);
        // set where salt will appear in hash //
        $saltStart = strlen($password);
        $saltHash = hash($mode, $this->email);
        // add salt into text hash at pass length position and hash it //
        if($saltStart > 0 && $saltStart < strlen($saltHash)) {
            $textHashStart = substr($textHash,0,$saltStart);
            $textHashEnd = substr($textHash,$saltStart,strlen($saltHash));
            $outHash = hash($mode, $textHashEnd.$saltHash.$textHashStart);
        } elseif($saltStart > (strlen($saltHash)-1)) {
            $outHash = hash($mode, $textHash.$saltHash);
        } else {
            $outHash = hash($mode, $saltHash.$textHash);
        }
        return $outHash;
    }

    /**
     * hook before save
     *
     *
     * @return boolen
     */
    protected function beforeSave() {
        if ($this->isNewRecord) {
            $this->password = $this->hashPassword($this->password);
        }
        return parent::beforeSave();
    }

    /**
     * Authenticates the password.
     * This is the 'authenticatePass' validator as declared in rules().
     */
    public function authenticatePass($attribute,$params) {

        if (!$this->hasErrors()) { // we only want to authenticate when no input errors
            Yii::import('application.components.PUserIdentity');
            $identity = new UserIdentity($this->email, $this->hashPassword($this->password));
            $identity->authenticate();

            switch ($identity->errorCode) {
                case UserIdentity::ERROR_NONE:
                    $duration = $this->rememberMe ? 3600*24*30 : 0; // 30 days
                    Yii::app()->user->login($identity, $duration);
                    break;

                case UserIdentity::ERROR_USERNAME_INVALID:
                    $this->addError('email','Email is incorrect.');
                    break;

                default: // UserIdentity::ERROR_PASSWORD_INVALID
                    $this->addError('password','Password is incorrect.');
                    break;
            }
        }
    }

    /**
     * function_description
     *
     * @param $code:
     *
     * @return
     */
    public function active_user($code) {
        if ($this->activationCode != $code) {
            return false;
        }

        $this->activated = true;
        if ($this->save(false)) {
            //login user
            $identity = new UserIdentity($this->email, $this->password);
            $identity->authenticate();
            Yii::app()->user->login($identity, 0);
            return true;
        } else {
            return false;
        }
    }

    /**
     * function_description
     *
     * @param $attribute:
     * @param $params:
     *
     * @return
     */
    public function check_password($attribute, $params) {
        $pass = $this->$attribute;
        $user = self::model()->findByAttributes(array(
                'email'=>$this->email));
        if($this->hashPassword($pass) != $user->password){
            $this->addError('old_password', '旧密码错误！');
        }
    }
    /**
     * function_description
     *
     * @param $attribute:
     * @param $params:
     *
     * @return
     */
    public function check_username($attribute, $params) {
        $user = self::model()->findByAttributes(array(
                'username'=>$this->nickname));
        if($user){
            $this->addError('nickname', '来晚一步，昵称已被其他用户抢先用了！');
        }
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function use_new_password() {
        if (!empty($this->new_password)) {
            $this->password = $this->hashPassword($this->new_password);
            $this->save(false);
        }
        return true;
    }


    public function generatePassword($length=20) {
        $chars = "abcdefghijkmnopqrstuvwxyz023456789";
        srand((double)microtime()*1000000);
        $i = 0;
        $pass = '' ;

        while ($i < $length) {
            $num = rand() % 33;
            $tmp = substr($chars, $num, 1);
            $pass .= $tmp;
            $i++;
        }
        return $pass;
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function generateResetPasswordCode() {
        $code = $this->generatePassword(16);
        $this->saveResetPasswordCode($code);
        $this->reset_password_code = $code;
        return $code;
    }

    /**
     * function_description
     *
     * @param $code:
     *
     * @return
     */
    public function saveResetPasswordCode($code) {
        $code = trim($code);
        $sql = "INSERT INTO {$this->reset_password_tablename} (`uid`,`code`,`created`) VALUES (:uid, :code, :created)";
        $command = $this->getDbConnection()->createCommand($sql);
        $command->bindValue(':uid', $this->id, PDO::PARAM_INT);
        $command->bindValue(':code', $code, PDO::PARAM_STR);
        $command->bindValue(':created', Time::checkDateTime(''), PDO::PARAM_STR);
        return $command->execute();
    }

    /**
     * function_description
     *
     * @param $code:
     *
     * @return
     */
    public static function findUserByResetPasswordCode($code) {
        $tableName = self::model()->reset_password_tablename;
        $sql = "SELECT `uid` FROM {$tableName} WHERE `code`=:code AND `created`>:created";
        $command = self::model()->getDbConnection()->createCommand($sql);
        $command->bindValue(':code', $code, PDO::PARAM_STR);
        $command->bindValue(':created', Time::checkDateTime('-1day'), PDO::PARAM_STR);
        $result = $command->queryRow();
        if (isset($result['uid'])) {
            return self::model()->findByPk(intval($result['uid']));
        } else {
            return null;
        }
    }

    /**
     * function_description
     *
     * @param $code:
     *
     * @return
     */
    public static function deleteResetPasswordCode($code) {
        $tableName = self::model()->reset_password_tablename;
        $sql = "DELETE FROM {$tableName} WHERE `code`=:code";
        $command = self::model()->getDbConnection()->createCommand($sql);
        $command->bindValue(':code', $code, PDO::PARAM_STR);
        return !!$command->execute();
    }

    /**
     * reset password check
     *
     *
     * @return
     */
    public function resetPassword($attributes, $params) {
        if (!$this->hasErrors()) {
            //use reset password code for user
            if (self::deleteResetPasswordCode($this->reset_password_code)) {
                $this->password = $this->hashPassword($this->password);
            } else {
                $this->addError('reset_password_code', 'can not find this reset code');
            }
        }

    }


    /**
     * get if user email has confirmed
     *
     *
     * @return
     */
    public function getActivated() {
        return $this->email_confirmed==null;
    }

    /**
     * set user activeated status
     *
     * @param val
     *
     * @return
     */
    public function setActivated($val) {
        if ($val)
            $this->email_confirmed = null;
        else {
            $this->email_confirmed = $this->generatePassword();
        }
    }

    public function getActivationCode() {
        return ($this->email_confirmed != null) ? $this->email_confirmed : false;
    }

    /**
     * get user link
     *
     *
     * @return
     */
    public function getUserlink() {
        $url = Yii::app()->createUrl('/user/view',array('username'=>$this->username));
        return "<a href='{$url}'>{$this->username}</a>";
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getResetPasswordLink() {
        return Yii::app()->createAbsoluteUrl('/user/recoverpass',
            array('code'=>$this->generateResetPasswordCode()));
    }



    protected function afterSave() {
        parent::afterSave();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'nickname' => "昵称",
            'old_password' => "旧密码",
            'new_password' => "新密码",
            'confirm_password' => "确认新密码",
            'password' => "密码",
            'password_repeat' => "重复密码"
        );
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function send_verify_email() {
        $to_send = $this->email;
        $subject = Yii::t("mii","User Active Mail");
        $content = Yii::app()->getViewRenderer()
            ->renderPartial('user/active', array('user'=>$this));
        return Yii::app()->sendMail->send($to_send,$subject,$content,'html');
    }

    /**
     * function_description
     *
     * @param $email:
     *
     * @return
     */
    public function resend_verify_mail($email) {
        $email = trim($email);
        $user = self::model()->findByAttributes(array(
                    'email'=>$email));
        if (empty($user) || $user->getActivated()) {
            return false;
        }

        return $user->send_verify_email();
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function send_recover_mail() {
        $to_send = $this->email;
        $subject = Yii::t('mii', 'Account Recover Mail');
        $content = Yii::app()->getViewRenderer()
            ->renderPartial('user/recover_email', array('user'=>$this));
        return Yii::app()->sendMail->send($to_send, $subject,$content,'html');
    }

    /**
     * function_description
     *
     * @param $username:
     *
     * @return
     */
    public function setUsername($username) {
        $this->username = $username;
        if ($this->validate(array('username'))) {
            return $this->save(false);
        }
        return false;
    }
        /**
     * function_description
     *
     * @param $uid:
     *
     * @return
     */
    public function getUserByName($username) {
        $criteria=new CDbCriteria;
        $criteria->condition = 'username=:username';
        $criteria->params =array(':username'=>$username);
        return $this->findAll($criteria);
    }

}