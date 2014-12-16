<?php
/**
 * This file contains the CStoryTestCase class. Which is based on CTestCase by Qiang Xue <qiang.xue@gmail.com>, part of Yii framework <www.yiiframework.com>.
 * @author Adam "Sidewinder" Klos  <adam.klosiu@gmail.com>
 * @license http://www.yiiframework.com/license/
 */

require_once('PHPUnit/Runner/Version.php');
require_once('PHPUnit/Autoload.php');
if (in_array('phpunit_autoload', spl_autoload_functions())) { // PHPUnit >= 3.7 'phpunit_alutoload' was obsoleted
    spl_autoload_unregister('phpunit_autoload');
    Yii::registerAutoloader('phpunit_autoload');
}

abstract class CStoryTestCase extends PHPUnit_Extensions_Story_TestCase
{
}