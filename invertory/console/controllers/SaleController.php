<?php
/**
 * user: liding
 * date: 14-4-17
 * time: 13:21
 */

namespace console\controllers;

use Yii;
use yii\console\Controller;

class SaleController extends Controller{

    /**
     * 狗市脚本 每小时执行一次
     * 修改狗市用户账户金额 删除竞价金额已经达到上限记录 生存新的竞价记录
     */
    public function actionHourly(){
        $message  = $this->hourly();
        $date = date('Y-m-d H:i');

        $success = '';
        $fail = '';
        foreach($message as $value){
            if($value['status'] === 1){
                $success .= $value['hint'] ."\n";
            }else{
                $fail .= $value['hint']."\n";
            }
        }

        //写日志
        if(!empty($success)){
            file_put_contents(Yii::$app->params['saleLog']['hourly_success'],$date."\n".$success."\n",FILE_APPEND);
        }
        if(!empty($fail)){
            file_put_contents(Yii::$app->params['saleLog']['hourly_error'],$date."\n".$fail."\n",FILE_APPEND);
        }
    }

    /**
     * 执行时间每天凌晨12以后
     * 统计狗市点击收入
     */
    public function actionDaily()
    {
        $message  = $this->daily();
        $date = date('Y-m-d');

        $success = '';
        $fail = '';
        foreach($message as $value){
            if($value['status'] === 1){
                $success .= $value['hint'] ."\n";
            }else{
                $fail .= $value['hint']."\n";
            }
        }

        //写日志
        if(!empty($success)){
            file_put_contents(Yii::$app->params['saleLog']['daily_success'],$date."\n".$success."\n",FILE_APPEND);
        }
        if(!empty($fail)){
            file_put_contents(Yii::$app->params['saleLog']['daily_error'],$date."\n".$fail."\n",FILE_APPEND);
            $location = '出错脚本位置在'.__FILE__;
            file_put_contents(Yii::$app->params['saleLog']['daily_error'],$location."\n",FILE_APPEND);
        }
    }


    /**
     * 每小时执行一次
     */
    private function hourly()
    {
        $message = [];
        $updateSaleUserAccount = $this->hourlyUpdateSaleUserAccount();
        if($updateSaleUserAccount){
            $message['updateSaleUserAccount']['status'] = 1;
            $message['updateSaleUserAccount']['hint'] = '调整用户账户金额成功';
        }else{
            $message['updateSaleUserAccount']['status'] = 0;
            $message['updateSaleUserAccount']['hint'] = '调整用户账户金额失败,此段时间没有产生竞价点击';
        }
        $this->hourlyDeleteSaleInfoBid(); //此操作可以不记录日志
        $this->hourlyInertSaleInfoBid(); //此操作可以不记录日志
        return $message;
    }


    /**
     * 每天执行一次
     * @return array
     */
    private function daily(){
        $message =[];
        $saleInfoValue =  $this->dailySetSaleInfoValue();
        if($saleInfoValue){
            $message['saleInfoValue']['status'] = 1;
            $message['saleInfoValue']['hint'] = '清空狗市竞价信息表当日累计金额成功';
        }else{
            $message['saleInfoValue']['status'] = 0;
            $message['saleInfoValue']['hint'] = '清空狗市竞价信息表当日累计金额失败';
        }
        $saleInfoBidValue = $this->dailySetSaleInfoBidValue();
        if($saleInfoBidValue){
            $message['saleInfoBidValue']['status'] = 1;
            $message['saleInfoBidValue']['hint'] = '清空狗市今天点击产生的金额成功';
        }else{
            $message['saleInfoBidValue']['status'] = 0;
            $message['saleInfoBidValue']['hint'] = '清空狗市今天点击产生的金额失败';
        }
        $insertBidSummary = $this->dailyInsertBidSummary();
        if($insertBidSummary){
            $message['insertBidSummary']['status'] = 1;
            $message['insertBidSummary']['hint'] = '生成昨天狗市竞价数据成功';
        }else{
            $message['insertBidSummary']['status'] = 0;
            $message['insertBidSummary']['hint'] = '生成昨天狗市竞价数据失败';
        }
        $insertBidSummaryGs = $this->dailyInsertBidSummaryGs();
        if($insertBidSummaryGs){
            $message['insertBidSummaryGs']['status'] = 1;
            $message['insertBidSummaryGs']['hint'] = '生成昨日狗市返利竞价信息数据成功';
        }else{
            $message['insertBidSummaryGs']['status'] = 0;
            $message['insertBidSummaryGs']['hint'] = '生成昨日狗市返利竞价信息数据失败';
        }
        return $message;
    }


    /**
     * 重设DB
     * @return null|object
     */
    private static function getDb(){
        return Yii::$app->get('dogdb');
    }


    /**
     * 更新狗市用户金额
     * @return int
     */
    private function hourlyUpdateSaleUserAccount()
    {
        $sql = "
            UPDATE dog_saleinfo_bid b
         LEFT JOIN dog_member_account m ON sale_userid=user_id
               SET b.user_money=m.user_money
               ";
        $row = static::getDb()
            ->createCommand($sql)
            ->execute();
        return $row;
    }

    /**
     * 删除dog_saleinfo_bid表中用户设置点击金额大于改用户的余额 和用户今日点击产生金额加上用户点击金额 大于该用户今日上限金额
     * @return int
     */
    private function hourlyDeleteSaleInfoBid()
    {
        $sql = "
            DELETE FROM dog_saleinfo_bid
                  WHERE sale_bid_click > user_money
                     OR sale_bid_today+sale_bid_click >sale_bid_daily
               ";
        $row = static::getDb()
            ->createCommand($sql)
            ->execute();
        return $row;
    }


    /**
     * 添加设置了狗市竞价的用户信息到狗市点击表中
     * @return int
     */
    private function hourlyInertSaleInfoBid()
    {
        $sql = "INSERT IGNORE INTO dog_saleinfo_bid(sale_id, sale_userid, sale_bid_click, sale_bid_daily, sale_bid_today, user_money)
                            SELECT sale_id,sale_userid,sale_bid_click, sale_bid_daily, sale_bid_today, user_money
                              FROM dog_saleinfo
                         LEFT JOIN dog_member_account
                                ON sale_userid=user_id
                             WHERE sale_status=2
                               AND sale_bid_daily>sale_bid_today+sale_bid_click
                               AND user_money>=sale_bid_click
                ";
        $row = static::getDb()
            ->createCommand($sql)
            ->execute();
        return $row;
    }

    /**
     * 清空竞价信息的当日累计金额，重新生成竞价信息表
     * @return int
     */
    private function dailySetSaleInfoValue()
    {
        $sql = "
            UPDATE dog_saleinfo
			   SET sale_bid_today=0
			 WHERE sale_status=2
			";
        $row = static::getDb()
            ->createCommand($sql)
            ->execute();
        return $row;
    }

    /**
     * 清空狗市今天点击产生的金额
     * @return int
     */
    private function dailySetSaleInfoBidValue()
    {
        $sql = " UPDATE dog_saleinfo_bid SET sale_bid_today=0";
        $row = static::getDb()
            ->createCommand($sql)
            ->execute();
        return $row;
    }

    /**
     * 生成昨天狗市竞价数据
     */
    private function dailyInsertBidSummary()
    {
        $yesterday = strtotime("yesterday");
        $today = strtotime("today");

        //删除昨天已有的数据
        $time = date('Y-m-d' ,$yesterday);
        $sql =" DELETE FROM dog_bid_summary WHERE rec_date = '$time'";
        static::getDb()->createCommand($sql)->execute();

        //统计生成昨天狗市竞价信息
        $sql = "
            INSERT IGNORE INTO dog_bid_summary(rec_date, rec_money, rec_click, rec_info_num, rec_user_num)
                        SELECT FROM_UNIXTIME(rec_cdate,'%Y-%m-%d') AS rec_date,  0-SUM(rec_money) AS income,  COUNT(*) AS click_count, COUNT(DISTINCT rec_target_id) AS info_num,  COUNT(DISTINCT rec_user_id) AS user_num
                          FROM dog_account_record
                         WHERE rec_type=31
                           AND rec_cdate>=$yesterday
                           AND rec_cdate<$today
                      GROUP BY rec_date
            ";
        $row = static::getDb()
            ->createCommand($sql)
            ->execute();
        return $row;
    }

    /**
     * 生成昨日狗市返利竞价信息数据
     */
    private function dailyInsertBidSummaryGs()
    {

        $yesterday = strtotime("yesterday");
        $today = strtotime("today");

        //删除昨天已有的数据
        $time = date('Y-m-d' ,$yesterday);
        $sql ="DELETE FROM dog_bid_summary_gs WHERE rec_date = '$time'";
        static::getDb()->createCommand($sql)->execute();
        //统计生成昨天狗市竞价返利信息
        $sql = "
            INSERT IGNORE INTO dog_bid_summary_gs(rec_date, rec_money, rec_click, rec_info_num, rec_user_num)
				  	    SELECT FROM_UNIXTIME(rec_cdate,'%Y-%m-%d') AS rec_date,  0-SUM(rec_money) AS income,  COUNT(*) AS click_count, COUNT(DISTINCT rec_target_id) AS info_num,  COUNT(DISTINCT rec_user_id) AS user_num
				    	  FROM dog_account_record
				         WHERE rec_type=45
				           AND rec_cdate>=$yesterday
				           AND rec_cdate<$today
				      GROUP BY rec_date
		    ";
        $row = static::getDb()
            ->createCommand($sql)
            ->execute();
        return $row;
    }
} 