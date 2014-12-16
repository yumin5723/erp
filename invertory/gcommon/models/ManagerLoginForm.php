<?php

/**
 * This is the model class for Login Form.
 * 
 * @version 1.0
 * @package cms.models.user
 *
 */
class ManagerLoginForm extends CFormModel
{
    public $username;
    public $password;
    public $rememberMe;

    private $_identity;

    /**
     * Declares the validation rules.
     * The rules state that username and password are required,
     * and password needs to be authenticated.
     */
    public function rules()
    {
        return array(
            // username and password are required
            array('username, password', 'required'),
            // rememberMe needs to be a boolean
            array('rememberMe', 'boolean'),
            // password needs to be authenticated
            array('password', 'authenticate'),
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return array(
            'username'=>Yii::t('cms','Username'),
            'password'=>Yii::t('cms','Password'),
            'rememberMe'=>Yii::t('cms','Remember me'),
        );
    }

    
    /**
     * Authenticates the password.
     * This is the 'authenticate' validator as declared in rules().
     */
    public function authenticate($attribute,$params)
    {
        if(!$this->hasErrors())
        {
            $this->_identity=new AdminIdentity($this->username,$this->password);
            if(!$this->_identity->authenticate())
                $this->addError('password','Incorrect username or password.');
        }
    }

    /**
     * Logs in the user using the given username and password in the model.
     * @return boolean whether login is successful
     */
    public function login()
    {
        if($this->_identity===null)
        {
            $this->_identity=new AdminIdentity($this->username,$this->password);
            $this->_identity->authenticate();
        }
        if($this->_identity->errorCode===AdminIdentity::ERROR_NONE)
        {
            $duration=$this->rememberMe ? 60*60*24*7 : 17400; // 7 days or default to 20 minutes
            Yii::app()->user->login($this->_identity,$duration);
            return true;
        }
        else
            return false;
    }
}
