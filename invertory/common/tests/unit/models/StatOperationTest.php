<?php

namespace common\tests\unit\models;

use Yii;
use yii\codeception\TestCase;
use backend\models\StatOperation;
use yii\test\DbTestTrait;


class StatOperTest extends TestCase
{
	use DbTestTrait;

	public function testGetAllOperationData()
	{
		$model = new StatOperation;
		$r = $model->getAllOperationData();
		$this->assertEmpty($r->key);
		$this->assertNotEmpty($r->query);
	}

}