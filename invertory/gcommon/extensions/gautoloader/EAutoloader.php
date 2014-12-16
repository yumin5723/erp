<?php
namespace gcommon\extensions\gautoloader;
// Credit: mindplay-dk https://gist.github.com/4234540
use Yii;
use yii\base\Component;
use gcommon\extensions\gautoloader\GAutoloader;

class EAutoloader extends Component
{

    private static $autoloader=null;

    /**
     * @return GAutoloader
     */
    public static function getAutoloader()
    {

        if (self::$autoloader===null) {
            self::$autoloader = new GAutoloader();
        }

        return self::$autoloader;

    }

    public static function autoload($className)
    {
        if (self::getAutoloader()->load($className)) {
            return true;
        }

        return Yii::autoload($className);
    }
}

spl_autoload_unregister(array('YiiBase','autoload'));

spl_autoload_register(array('gcommon\extensions\gautoloader\EAutoloader','autoload'));


