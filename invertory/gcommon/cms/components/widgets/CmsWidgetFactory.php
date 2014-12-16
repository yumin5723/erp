<?php
namespace gcommon\cms\components\widgets;
use Yii;
use yii\base\Widget;
// use gcommon\cms\components\widgets\CustomBlockWidget;
// Yii::import("gcommon.cms.components.widgets.*");
class CmsWidgetFactory extends Widget {
    protected static $widgets = array(
        1 => '\gcommon\cms\components\widgets\CustomBlockWidget',
        2 => '\gcommon\cms\components\widgets\ObjectListWidget',
        3 => 'gameList',
        4 => '\gcommon\cms\components\widgets\PictureListWidget',
        5 =>'\gcommon\cms\components\widgets\DataBlockWidget',
        'categorylist'=>'\gcommon\cms\components\widgets\CategorylistWidget',
        'taglist'=>'\gcommon\cms\components\widgets\TaglistWidget',
    );

    /**
     * return widget by widget type
     *
     * @param $widget_type:
     * @param array $params:
     *
     * @return
     */
    public static function factory($widget_type_id, array $params) {
        // get widget class
        if (!isset(self::$widgets[$widget_type_id])) {
            return null;
        }
        $classname = self::$widgets[$widget_type_id];
        // var_dump($classname);exit;
        // $widget = new \gcommon\cms\components\widgets\PictureListWidget;
        $widget = new $classname;
        foreach ($params as $name=>$value) {
            if (property_exists($widget,$name)) {
                $widget->$name = $value;
            }
        }
        $widget->init();
        return $widget;
    }


}