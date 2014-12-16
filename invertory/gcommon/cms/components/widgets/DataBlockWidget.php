<?php
namespace gcommon\cms\components\widgets;
use Yii;
use gcommon\cms\components\CmsRenderer;
use gcommon\cms\components\widgets\CmsWidget;
use gcommon\cms\models\Block;
class DataBlockWidget extends CmsWidget {
    public $datasource;

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
        if($this->datasource == null){
            throw new CmsException("datablock must have a data source...");
        }
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
        throw new Exception("widget must have a template", 500);
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
        // var_dump($this->datasource);exit;
        // 
        //bbsfocus|column:
        if(strpos("|",$this->datasource) !== false){
            $params = null;
        }else{
            list($class,$params) = explode("|", $this->datasource);
        }
        
        $model = new $class;
        $data = $model->getData($params);
        if (empty($viewRender)) {
            throw new CmsException("ActiveWidget must use app 'stringRender'.");
        }
        return $viewRender->render(null,$template,array("objs"=>$data));
    }
}