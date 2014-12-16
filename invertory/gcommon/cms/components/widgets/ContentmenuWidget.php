<?php
namespace gcommon\cms\components\widgets;
use gcommon\cms\models\Oterm;
use gcommon\cms\models\Object;
class ContentmenuWidget extends CmsWidget {

    public $listmenu = "";
    public $categoryid = 0;
    /**
     * function_description
     *
     *
     * @return
     */
    public function run() {
        return $this->getContentMenu();
    }
    /**
     * function_description
     *
     *
     * @return
     */
    public function getContentMenu() {
        if($this->listmenu == "true"){
            $oterm = new Oterm;
            $categories = $oterm->getLevelByTermId($this->categoryid);
        }else{
            $object = new Object;
            $categories = $object->getObjectTermById($this->id);
        }
        $html = '当前位置：<a class="a3" href="/">资讯</a><span>&gt;</span>';
        if(empty($categories)){
            return "";
        }
        foreach($categories as $key=>$category){
            $name = $category['term_name'];
            $url = $category['short_name'];
            $id = $category['id'];
            $html .= "<a class='a3' href='/{$url}'>$name</a><span>&gt;</span>";
        }
        if($this->listmenu == "false"){
            $html .= '正文';
        }
        return $html;
    }


}