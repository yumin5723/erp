<?php

/**
 * This is the model class for table "manager".
 *
 * The followings are the available columns in table 'manager':
 * @property integer $id
 * @property string $email
 * @property string $username
 * @property string $password
 * @property string $last_login_time
 * @property string $created
 * @property string $modified
 */
class Manager extends AdminActiveRecord
{
    public $password_repeat;
    public $old_password;
    public $new_password;
    /**
     * Returns the static model of the specified AR class.
     * @return Manager the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'manager';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('email','email'),
            array('email, username, password', 'required'),
            array('email, username, password', 'length', 'max'=>128),
            array('email, username', 'unique'),
            array('created,modified','safe'),
            ///array('password', 'compare'),
//            array('password_repeat','compare','compareAttribute'=>'password','on'=>'create'),
            array('old_password,new_password,password_repeat','required','on'=>'changepass'),
           array('old_password','check_password','on'=>'changepass'),
            array('new_password','compare','compareAttribute'=>'password_repeat','on'=>'changepass'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, email, username, password, last_login_time, created, modified', 'safe', 'on'=>'search'),
        );
    }
    public function behaviors()
    {
        return CMap::mergeArray(
            parent::behaviors(),
            array(
              'CTimestampBehavior' => array(
                    'class'               => 'zii.behaviors.CTimestampBehavior',
                    'createAttribute'     => 'created',
                    'updateAttribute'     => 'modified',
                    'timestampExpression' => 'date("Y-m-d H:i:s")',
                    'setUpdateOnCreate'   => true,
                ),
           )
        );
    }
    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'email' => 'Email',
            'username' => 'Username',
            'password' => 'Password',
            'last_login_time' => 'Last',
            'created' => 'Created',
            'modified' => 'Modified',
            'old_password'=>'Old password',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('email',$this->email,true);
        $criteria->compare('username',$this->username,true);
        $criteria->compare('password',$this->password,true);
        $criteria->compare('last_login_time',$this->last_login_time,true);
        $criteria->compare('created',$this->created,true);
        $criteria->compare('modified',$this->modified,true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria'=>$criteria,
        ));
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
    public function hashPassword($password) {
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
            $this->created = date("Y-m-d H:i:s");
            $this->modified = date("Y-m-d H:i:s");
        }
        return parent::beforeSave();
    }

    /**
     * function_description
     *
     * @param $attributes:
     *
     * @return
     */
    public function updateAttrs($attributes) {
        $attrs = array();
        if (!empty($attributes['email']) && $attributes['email'] != $this->email) {
            $attrs[] = 'email';
            $this->email = $attributes['email'];
        }
        if (!empty($attributes['username']) && $attributes['username'] != $this->email) {
            $attrs[] = 'username';
            $this->username = $attributes['username'];
        }

        if (!empty($attributes['password'])) {
            $attrs[] = 'password';
            $this->password = $attributes['password'];
            $this->password_repeat = $attributes['password_repeat'];
        }
        if ($this->validate($attrs)) {
            if (in_array('password', $attrs)) {
                $this->password = $this->hashPassword($this->password);
            }

            return $this->save(false);
        } else {
            return false;
        }
    }
    public function check_password($attribute,$params){
        $pass = $this->$attribute;
        $user = self::model()->findByAttributes(array(
            "email"=>$this->email
            ));
        if($this->hashPassword($pass)!= $user->password){
            $this->addError("old_password","旧密码错误");
        }
    }


}