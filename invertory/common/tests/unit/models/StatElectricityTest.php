<?php

namespace common\tests\unit\models;

use Yii;
use yii\codeception\TestCase;
use backend\models\StatElectricity;
use yii\test\DbTestTrait;


class StatElectricityTest extends TestCase
{
	use DbTestTrait;

	public function testGetAllOperationData()
	{
		$model = new StatElectricity;
		$r = $model->getAllElectricityData();
		$this->assertEmpty($r->key);
		$this->assertNotEmpty($r->query);
	}

}