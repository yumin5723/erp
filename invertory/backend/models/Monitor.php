<?php
namespace backend\models;

use yii\db\ActiveRecord;
use yii\helpers\Security;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
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
class Monitor extends ActiveRecord
{
	/**
	 * @var string the raw password. Used to collect password input and isn't saved in database
	 */
	public function behaviors()
	{
		return [
			'timestamp' => [
				'class' => 'yii\behaviors\TimestampBehavior',
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['create_time', 'update_time'],
					ActiveRecord::EVENT_BEFORE_UPDATE => 'update_time',
				],
			],
		];
	}
	public static function tableName(){
		return "monitoring_point";
	}
	/**
	 * @return int|string|array current user ID
	 */
	public function getId()
	{
		return $this->getPrimaryKey();
	}
	public function rules()
	{
		return [
			['desc', 'required'],
		];
	}
	public function getAllData(){
		$provider = new ActiveDataProvider([
	  		'query' => static::find()->orderby('id desc'),
			'sort' => [
                'attributes' => ['id',],
            ],
		    'pagination' => [
		        'pageSize' => 30,
		    ],
	    ]);
		return $provider;
	}

	public function updateAttrs($attributes){
        $attrs = array();
        if (!empty($attributes['desc']) && $attributes['desc'] != $this->desc) {
            $attrs[] = 'desc';
            $this->desc = $attributes['desc'];
        }
        if ($this->validate($attrs)) {
            return $this->save(false);
        } else {
            return false;
        }
	}
}
