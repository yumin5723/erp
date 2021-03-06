<?php

namespace common\tests\unit\models;

use Yii;
use yii\codeception\TestCase;
use common\models\User;
use yii\test\DbTestTrait;

class LoginFormTest extends TestCase
{
	use DbTestTrait;
	use \Codeception\Specify;

	protected function setUp()
	{
		parent::setUp();
		// uncomment the following to load fixtures for table tbl_user
		$this->loadFixtures(['tbl_user']);
	}

	public function testLoginNoUser()
	{
		$model = $this->mockUser(null);

		$model->username = 'some_username';
		$model->password = 'some_password';

		$this->specify('user should not be able to login, when there is no identity' , function () use ($model) {
			$this->assertFalse($model->login());
			$this->assertTrue(Yii::$app->user->isGuest,'user should not be logged in');
		});
	}

	public function testLoginWrongPassword()
	{
		$model = $this->mockUser(new User);

		$model->username = 'demo';
		$model->password = 'wrong-password';

		$this->specify('user should not be able to login with wrong password', function () use ($model) {
			$this->assertFalse($model->login());
			$this->assertArrayHasKey('password',$model->errors);
			$this->assertTrue(Yii::$app->user->isGuest,'user should not be logged in');
		});
	}

	public function testLoginCorrect()
	{
		$model = $this->mockUser(new User(['password' => 'demo']));

		$model->username = 'demo';
		$model->password = 'demo';

		$this->specify('user should be able to login with correct credentials', function() use ($model) {
			$this->assertTrue($model->login());
			$this->assertArrayNotHasKey('password',$model->errors);
			$this->assertFalse(Yii::$app->user->isGuest,'user should be logged in');
		});
	}

	private function mockUser($user)
	{
		$loginForm = $this->getMock('common\models\LoginForm',['getUser']);
		$loginForm->expects($this->any())->method('getUser')->will($this->returnValue($user));
		return $loginForm;
	}

}