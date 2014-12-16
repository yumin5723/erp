<?php
Yii::import('common.components.payment.Payment');
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-12-06 at 22:27:26.
 */
class PaymentTest extends CDbTestCase
{
    public $fixtures = array(
        'orders'=>'Order',
    );
    /**
     * @var Payment
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = Yii::app()->payment;
        parent::setUp();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Payment::directPay
     * @todo   Implement testDirectPay().
     */
    public function testDirectPay()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Payment::generateReturnData
     * @todo   Implement testGenerateReturnData().
     */
    public function testGenerateReturnData()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Payment::getHashString
     * @todo   Implement testGetHashString().
     */
    public function testGetHashString()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Payment::getDirectOrdersNeedPay
     * @todo   Implement testGetDirectOrdersNeedPay().
     */
    public function testGetDirectOrdersNeedPay()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Payment::CardPayForOrder
     * @todo   Implement testCardPayForOrder().
     */
    public function testCardPayForOrder()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Payment::getChannelById
     * @todo   Implement testGetChannelById().
     */
    public function testGetChannelById()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Payment::getChannelByMethod
     * @todo   Implement testGetChannelByMethod().
     */
    public function testGetChannelByMethod()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Payment::receiveCallback
     * @todo   Implement testReceiveCallback().
     */
    public function testReceiveCallback()
    {
        $channel_id = "yeecardpay";
        $params = array(
            'r0_Cmd'=>'ChargeCardDirect',
            'r1_Code'=>'1',
            'p1_MerId'=>'10001126856',
            'p2_Order'=>'1000000004',
            'p3_Amt'=>'50.00',
            'p4_FrpId'=>'UNICOM',
            'p5_CardNo'=>'123889435694567974569',
            'p6_confirmAmount'=>'50',
            'p7_realAmount'=>'50',
            'p8_cardStatus'=>'',
            'p9_MP'=>'{"pay_id":"1"}',
            'pb_BalanceAmt'=>'0',
            'pc_BalanceAct'=>'',
            'hmac'=>'9d8628e7e5dd2ca1f15fda179805e1f7',
        );
        $t1 = time();
        $ret = Yii::app()->payment->receiveCallback($channel_id, $params);
        $t2 = time();
        $this->assertEquals("success", $ret);
        $pay = Pay::model()->findByPk('1');
        $this->assertEquals($pay->status, Pay::STATUS_GOT_CALLBACK);
        $this->assertEquals($pay->pay_callback, json_encode($params));
        $this->assertLessThanOrEqual($t1, strtotime($pay->pay_callback_time));
        $this->assertGreaterThanOrEqual($t2, strtotime($pay->pay_callback_time));
    }
}
