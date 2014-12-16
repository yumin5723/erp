<?php

Yii::app()->getComponent('openid');

class OpenidTest extends CDbTestCase {

    public $fixtures = array(
        'user' => 'User',
        'app' => 'App',
        'openuser' => 'OpenUser',
    );

    /**
     * function_description
     *
     *
     * @return
     */
    public function setUp() {
        parent::setUp();
    }

    /**
     * function_description
     *
     *
     * @return
     */
    /**
     * function_description
     *
     *
     * @return
     */
    public function testGetUserOpenidForApp() {
        // test for new openuser
        $openuser1 = $this->openuser['1'];
        $openid1 = Yii::app()->openid->getUserOpenidForApp($openuser1['app_id'],$openuser1['uid']);
        $this->assertEquals($openuser1['openid'], $openid1);
        // test for exists openuser

        $app_id = 3333;
        $uid = 4444;
        $openid2 = Yii::app()->openid->getUserOpenidForApp($app_id, $uid);
        $this->assertGreaterThan(1, strlen($openid2));
        $this->assertNotEquals($openid1,$openid2);
    }


}