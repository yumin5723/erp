<?php
namespace gcommon\cms\components\widgets;
use Yii;
use gcommon\cms\components\CmsRenderer;
use gcommon\cms\components\widgets\CmsWidget;
use gcommon\cms\models\Block;
class PictureListWidget extends CmsWidget {


    public $picture = array();

    public $default_template = <<<EOF
    <ul>
    {% for obj in objs %}
      <li>{{ obj.imgname }}</li>
      <li><a href="{{ obj.imglink }}">{{ obj.imgname }}</a></li>
      <li><img src="{{ obj.url }}"></li>
      <li>{{ obj.time }}</li>
      <li>{{ obj.desc }}</li>
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
     * render html use objects and template
     *
     *
     * @return
     */
    public function renderContent() {
        $template = $this->getTemplate();
        $viewRender = Yii::$app->get("stringview");
        if (empty($viewRender)) {
            throw new CmsException("ActiveWidget must use app 'stringRender'.");
        }
        $this->picture = $this->array_sort($this->picture,'order');
        return $viewRender->render(null,$template,array("objs"=>$this->picture));
    }
    public function array_sort($arr,$keys,$type='desc'){
        $keysvalue = $new_array = array();
        foreach ($arr as $k=>$v){
            $keysvalue[$k] = $v[$keys];
        }
        if($type == 'asc'){
            asort($keysvalue);
        }else{
            arsort($keysvalue);
        }
        reset($keysvalue);
        foreach ($keysvalue as $k=>$v){
            $new_array[$k] = $arr[$k];
        }
        return $new_array;
    } 


}