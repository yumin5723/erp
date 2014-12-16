<?php

class ReadSessionTest extends CTestCase {
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
    public function testGetSessionById() {
        $config = array(
            'class' => 'gcommon.components.ReadSession',
            'session_path' => dirname(__FILE__)."/",
        );
        $rs = Yii::createComponent($config);
        /* test session id not exists*/
        $session_id = "123123123";
        $this->assertNull($rs->getSessionById($session_id));

        /* test session exists */
        $session_id = "89rloudov1qgmdb0jkqo782c22";
        $sess = $rs->getSessionById($session_id);
        $this->assertTrue(is_array($sess));
        $this->assertArrayHasKey("212412412asfsadfsdf__id", $sess);

        /* empty sesssion key */
        $session_id = "123456789";
        $sess = $rs->getSessionById($session_id);
        $this->assertNull($sess);
    }



}
