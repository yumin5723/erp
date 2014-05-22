<?php
/**
 * User: liding
 * Date: 14-4-10
 * Time: 13:26
 */

namespace common\models;

use common\components\MallActiveRecord;

class GoodsType extends MallActiveRecord{

    public static function tableName()
    {
        return 'goods_type';
    }

} 