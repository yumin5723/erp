<?php
namespace gcommon\cms\components\widgets;
use Yii;
use gcommon\cms\components\CmsRenderer;
use gcommon\cms\components\widgets\CmsWidget;
use gcommon\cms\models\Object;
use gcommon\cms\models\Tag;
use gcommon\cms\models\TagRelationships;
use gcommon\cms\components\SubPages;
class TaglistWidget extends CmsWidget {
    public $tag_id;

    public $count;

    public $page;
    public $offset = 0;
    public $default_count = Tag::LIST_PAGE_DISPLAY_COUNT;
    public $default_template = <<<EOF
    {% for obj in data.objs %}
    <li>
        <div class="title clearfix">
          <h4>
            <a href="{{ obj.url }}" target="_blank" class="a3">{{ obj.object_name }}</a>
          </h4><a href="{{ obj.url }}" target="_blank" class="raty"></a>
        </div>
        <div class="content">
          <div class="author">
            <span class="date">{{ obj.object_date|date('m-d')}}</span> <span class="read">6772阅读</span> <span class="num">23评论</span>
          </div>
          <div class="word">
          {{ obj.object_excerpt }}
            <a href="{{ obj.url }}" target="_blank" class="ao">[详情]</a>
          </div>
          <dl class="tag clearfix">
            <dt>
              标签：
            </dt>
            <dd>
              {% for tag in obj.objecttags %}
                 {% if tag.frequency == 2 %}
                    <a href="{{ tag.url }}" {% if tag.id == data.tag_id %}class="cur" {% endif %}>{{ tag.name }}</a>
                 {% else %}
                    <a href="/{{ tag.url }}" {% if tag.id == data.tag_id %}class="cur" {% endif %}>{{ tag.name }}</a>
                 {% endif %}
              {% endfor %}
            </dd>
          </dl>
        </div>
      </li>
    {% endfor %}
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
        if (empty($this->tag_id)) {
            throw new CmsException("TagListWidget can not find a tag id");
        }

        $tag = new Tag;
        $objects = $tag->fetchObjectsByTagId($this->tag_id,$this->getCount(),$this->page);
        $sum = $tag->getObjectsCountByTagId($this->tag_id);
        $sub_pages = 6;
        $url = "/tag/{$this->tag_id}_";
        $subPages=new SubPages($this->getCount(),$sum,$this->page,$sub_pages,$url,2);
        $p = $subPages->show_SubPages(2);
        return array("objs"=>$objects,"p"=>$p,"tag_id"=>$this->tag_id);
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
        $template = $this->default_template;
        $viewRender = Yii::$app->get("stringview");
        if (empty($viewRender)) {
            throw new CmsException("TagListWidget must use app 'stringview'.");
        }
        return $viewRender->render(null,$template,array("data"=>$data));
    }
}