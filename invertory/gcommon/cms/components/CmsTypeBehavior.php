<?php
namespace gcommon\cms\components;
use Yii;
use yii\base\Behavior;
/**
 * cms objects type definition
 */

class CmsTypeBehavior extends Behavior {

    /**
     * function_description
     *
     *
     * @return
     */
    public function getObjType() {
        if (is_a($this->owner, "gcommon\cms\models\Block")) {
            return "block";
        }

        if (is_a($this->owner, "gcommon\cms\models\Page")) {
            return "page";
        }
        if (is_a($this->owner, "gcommon\cms\models\Template")) {
            return "template";
        }

        if (is_a($this->owner, "gcommon\cms\models\Object")) {
            return "object";
        }

        if (is_a($this->owner, "gcommon]cms]models\DbActiveRecord")) {
            return "db";
        } 

        return null;
    }

}
