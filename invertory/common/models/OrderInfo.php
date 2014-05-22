<?php
/**
 * user: liding
 */

namespace common\models;

use common\components\MallActiveRecord;

class OrderInfo extends MallActiveRecord{

    protected $pk = 'info_id';

    public static function tableName()
    {
        return 'order_info';
    }

} 