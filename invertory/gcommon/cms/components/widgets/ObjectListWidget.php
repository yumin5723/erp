<?php
namespace gcommon\cms\components\widgets;
use Yii;
use gcommon\cms\components\widgets\CmsWidget;
use gcommon\cms\models\Object;
class ObjectListWidget extends CmsWidget {
    public $category_id;

    public $count;

    public $offset = 0;
    public $default_count = 10;
    public $default_template = <<<EOF
    <ul>
    {% for obj in objs %}
      <li>{{ obj.object_name }}</li>
    {% endfor %}
    </ul>
EOF;

    /**
     * function_description
     *
     *
     * @return
     */
    public function run() {
        return $this->renderContent();
    }

    /**
     * get objects for render
     *
     *
     * @return
     */
    protected function getObjects() {
        if (empty($this->category_id)) {
            throw new CmsException("ObjectListWidget can not find a category id");
        }
        $object = new Object;
        return $object->fetchObjectsByTermId($this->category_id, $this->getCount(),0);
    }

    /**
     * function_description
     *
     *
     * @return
     */
    protected function getTemplate() {
        if (!empty($this->block_content)) {
            return $this->block_content;
        }
        return $this->default_template;
    }

    /**
     * function_description
     *
     *
     * @return
     */
    protected function getCount() {
        if (!empty($this->count) || $this->count != 0) {
            return $this->count;
        }
        return $this->default_count;
    }

    /**
     * render html use objects and template
     *
     *
     * @return
     */
    public function renderContent() {
        $objs = $this->getObjects();
        $template = $this->getTemplate();
        $viewRender = Yii::$app->get("stringview");
        if (empty($viewRender)) {
            throw new CmsException("ObjectListWidget must use app 'stringView'.");
        }
        return $viewRender->render(null,$template,array("objs"=>$objs));
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getDependentCategoryIds() {
        return array($this->category_id);
    }



}