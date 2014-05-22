<?php
namespace backend\models;

use yii\db\ActiveRecord;
use yii\helpers\Security;
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
class Post extends ActiveRecord
{
	public static function tableName(){
		return "manager";
	}
}