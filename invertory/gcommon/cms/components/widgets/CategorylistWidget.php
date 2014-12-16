<?php
namespace gcommon\cms\components\widgets;
use Yii;
use gcommon\cms\components\CmsRenderer;
use gcommon\cms\components\widgets\CmsWidget;
use gcommon\cms\models\Object;
use gcommon\cms\models\Oterm;
use gcommon\cms\components\NewslistSubPages;
class CategorylistWidget extends CmsWidget {
    public $category_id;

    public $count;

    public $page;
    public $offset = 0;
    public $default_count = Oterm::LIST_PAGE_DISPLAY_COUNT;
    public $default_template = <<<EOF
    <ul class="list_1">
         {% for obj in data.objs %}
              <li class="clearfix">
                <a class="a3" target="_blank" href="{{ obj.objects.url }}">{{ obj.objects.object_name }}</a> <span>[{{obj.objects.object_date|date("Y-m-d")}}]</span>
              </li>
         {% endfor %}
    </ul>
    {% if data.p is not empty %}
    <div class="mpage">
        <div class="pages clearfix">
            {{ data.p|raw }}
        </div>
    </div>
    {% endif %}
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
        $oterm = Oterm::findOne($this->category_id);
        $object = new Object;
        $objects = $object->fetchObjectsByTermId($this->category_id,$this->getCount(),$this->page);
        $sum = $object->getObjectsCountByTermId($this->category_id);
        $sub_pages = 6;
        if(empty($oterm) || $oterm->short_name == ""){
            $url = "/list/{$this->category_id}_";
        }else{
            $url = "/".$oterm->short_name."/";
        }
        $subPages=new NewslistSubPages($this->getCount(),$sum,$this->page,$sub_pages,$url,2);
        $p = $subPages->show_SubPages(2);
        return array("objs"=>$objects,"p"=>$p);
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
        $data = $this->getObjects();
        $template = $this->getTemplate();
        $viewRender = Yii::$app->get("stringview");
        if (empty($viewRender)) {
            throw new CmsException("ObjectListWidget must use app 'stringview'.");
        }
        return $viewRender->render(null,$template,array("data"=>$data));
    }
}