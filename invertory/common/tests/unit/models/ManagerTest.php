<?php

namespace common\tests\unit\models;

use Yii;
use yii\codeception\TestCase;
use backend\models\Manager;
use backend\models\LoginForm;
use yii\test\DbTestTrait;

class ManagerTest extends TestCase
{
	use DbTestTrait;
	protected function setUp()
	{
		parent::setUp();
		$this->loadFixtures(['manager']);
	}
	public function testLoginNoUser()
	{
		$model = new LoginForm;
		$model->username = '';
		$model->password = '';
		$this->assertFalse($model->login());
		$this->assertTrue(Yii::$app->user->isGuest,'user should not be logged in');
	}
	public function testLoginWrongPassword()
	{
		$model = new LoginForm;

		$model->username = 'demo';
		$model->password = 'wrong-password';

		$this->assertFalse($model->login());
		$this->assertArrayHasKey('password',$model->errors);
		$this->assertTrue(Yii::$app->user->isGuest,'user should not be logged in');
	}

	public function testLoginCorrect()
	{
		$model = new LoginForm;

		$model->username = 'admin';
		$model->password = '123456';

		$this->assertTrue($model->login());
		$this->assertArrayNotHasKey('password',$model->errors);
		$this->assertFalse(Yii::$app->user->isGuest,'user should be logged in');
	}

	public function testGetAllData(){
		$model = new Manager;
		$r = $model->getAllData();
		$this->assertEmpty($r->key);
		$this->assertNotEmpty($r->allModels);
	}

	public function testCreateCorrectAttrs(){
		$model = new Manager;
		$params = ['username'=>'goumintesting','password'=>'123456','email'=>'email@135.com'];
		$r = $model->updateAttrs($params);
		$this->assertTrue($r);
	}

	public function testCreateWrongAttrs(){
		$model = new Manager;
		$params = ['username'=>'goumintesting','password'=>'123456','email'=>'@135.com'];
		$r = $model->updateAttrs($params);
		$this->assertFalse($r);
	}

	public function testUpdateCorrectAttrs(){
		$model = Manager::find(['id'=>4]);
		$params = ['username'=>'goumintesting4','password'=>'123456','email'=>'email@135.com'];
		$r = $model->updateAttrs($params);
		$this->assertTrue($r);
	}

	public function testUpdateWrongAttrs(){
		$model = Manager::find(['id'=>5]);
		$params = ['username'=>'goumintesting5','password'=>'123456','email'=>'@135.com'];
		$r = $model->updateAttrs($params);
		$this->assertFalse($r);
	}

	public function testDeleteUser()
	{
		$model = new Manager;
		$uid =2;
		$r = $model->deleteUser($uid);
		$this->assertTrue($r);
	}
}