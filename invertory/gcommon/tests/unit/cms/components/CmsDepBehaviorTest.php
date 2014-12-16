<?php

class CmsDepBehaviorTest extends CDbTestCase {
    public $fixtures = array(
        'page' => 'Page',
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
    public function testUpdateDependentBlockByHtml() {
        $p = $this->page(1);
        /**
         * from [1,2] to [1,3,4]
         */
        $this->assertEquals($p->getCurrentDepBlockIds(), [1,2]);
        $this->assertTrue($p->UpdateDependentBlockByHtml($p->content));
        $this->assertEquals($p->getCurrentDepBlockIds(), [1,3,4]);
    }

}