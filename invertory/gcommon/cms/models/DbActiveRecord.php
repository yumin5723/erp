<?php
namespace gcommon\cms\models;

abstract class DbActiveRecord extends CmsActiveRecord implements DataSourceInterface
{
    abstract public static function tableName();
    abstract public function getData($params = null);

    public function behaviors() {
        return BaseArrayHelper::merge(
            parent::behaviors(),
            [
                'CmsEventBehavior' => [
                    'class' => 'gcommon\cms\components\CmsEventBehavior',
                ],
            ]
        );
    }

}
