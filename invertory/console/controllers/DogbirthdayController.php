<?php
/**
 * user: liding
 * date: 14-4-28
 */

namespace console\controllers;

use Yii;
use yii\console\Controller;

class DogbirthdayController extends Controller{


    public function actionRunscript()
    {
        $this->updateDogBirthday();
        $this->addDogBirthday();
    }


    /**
     * 清空狗狗生日
     */
    private function updateDogBirthday()
    {
        $sql = "UPDATE dog_doginfo SET dog_in_birthday=0";
        $row = static::getDb()
                ->createCommand($sql)
                ->execute();
        return $row;
    }

    /**
     * 获取最近7天过生日的狗狗
     */
    private function addDogBirthday()
    {
        for($i=0;$i<7;$i++){
            $month = date('n',time()+$i*24*3600);
            $day = date('j',time()+$i*24*3600);
            $year = date('Y',time()+$i*24*3600);
            $date = strtotime("$year-$month-$day");
            $sql = "UPDATE dog_doginfo SET dog_in_birthday=1,dog_birth_time = $date WHERE dog_birth_m = $month AND dog_birth_d = $day";
            $row = static::getDb()
                   ->createCommand($sql)
                   ->execute();
        }
    }

    /**
     * 重设DB
     * @return null|object
     */
    private static function getDb(){
        return Yii::$app->get('dogdb');
    }

} 