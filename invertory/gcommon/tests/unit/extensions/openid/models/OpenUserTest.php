<?php
Yii::app()->getComponent('openid');

class OpenUserTest extends CDbTestCase {
    public $fixtures = array(
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
    public function testCreateNew() {
        $app_id = 100001;
        $uid = 19993;
        $openuser = OpenUser::model()->createNew($app_id, $uid);
        $this->assertTrue($openuser instanceof OpenUser);
        $this->assertEquals($app_id, $openuser->app_id);
        $this->assertEquals($uid, $openuser->uid);
        $this->assertGreaterThan(1, strlen($openuser->openid));
    }


}