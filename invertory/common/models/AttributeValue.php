<?php
/**
 * User: liding
 * Date: 14-4-10
 * Time: 11:16
 */

namespace common\models;

use common\components\MallActiveRecord;

class AttributeValue extends MallActiveRecord{

    public static function tableName()
    {
        return 'attribute_value';
    }
} 