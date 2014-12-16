<?php
/**
 * Delete a file or recursively delete a directory
 *
 * @param string $str Path to file or directory
 */
function recursiveDelete($str){
    if(is_file($str)){
	return @unlink($str);
    }
    elseif(is_dir($str)){
	$scan = glob(rtrim($str,'/').'/*');
	foreach($scan as $index=>$path){
	    recursiveDelete($path);
	}
	return @rmdir($str);
    }
}


Yii::import("common.components.Publisher");

class PublisherStory extends CStoryTestCase
{
    protected $_htmlPath="/tmp/html";
    protected $_staticPath="/tmp/static";

    /**
     * function_description
     *
     *
     * @return
     */
    public function setUp() {
	// clear tmp dir
	recursiveDelete($this->_htmlPath);
	mkdir($this->_htmlPath, 0755);
	recursiveDelete($this->_staticPath);
	mkdir($this->_staticPath, 0755);
	return parent::setUp();
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function initPublisher() {
	$this->given('new publisher')
	     ->when("set static base url", "http://cdn.domain.com/")
	     ->and('set html base url', "http://test.domain.com/")
	     ->and("set html path", $this->_htmlPath)
	     ->and("set static path", $this->_staticPath);
	return $this;
    }

    /**
    * @scenario
    */
    public function publishImageNotExists() {
	$this->initPublisher()
	     ->and("publish image", dirname(__FILE__)."/ico_3.png", "image/ico_3.png")
	     ->then("return should be", "http://cdn.domain.com/image/ico_3.png");
    }

    /**
     * @scenario
     */
    public function publisImageExistsOne() {
	$this->initPublisher()
	     ->and("publish image", dirname(__FILE__)."/ico_3.png", "image/ico_3.png")
	     ->and("publish image", dirname(__FILE__)."/ico_3.png", "image/ico_3.png")
	     ->then("return should be", "http://cdn.domain.com/image/ico_3--1--.png");
    }

    /**
     * @scenario
     */
    public function publishImageExistsTwo() {
	$this->initPublisher()
	     ->and("publish image", dirname(__FILE__)."/ico_3.png", "image/ico_3.png")
	     ->and("publish image", dirname(__FILE__)."/ico_3.png", "image/ico_3.png")
	     ->and("publish image", dirname(__FILE__)."/ico_3.png", "image/ico_3.png")
	     ->then("return should be", "http://cdn.domain.com/image/ico_3--2--.png");
	}

    /**
     * @scenario
     */
    public function publishJsFile() {
	$this->initPublisher()
	     ->and("publish js", dirname(__FILE__)."/js_2.js", "js/js_2.js")
	     ->then("return should be", "http://cdn.domain.com/js/js_2.js");
    }

    /**
     * @scenario
     */
    public function publishCssFile() {
	$this->initPublisher()
	     ->and("publish css", dirname(__FILE__)."/css_2.css", "css/css_2.css")
	     ->then("return should be", "http://cdn.domain.com/css/css_2.css");
    }

   /**
     * @scenario
     */
    public function publishAnEntirePage() {
	$this->initPublisher()
	     ->and("publish page", dirname(__FILE__)."/test_html.rar", "test.html")
	     ->then('return should be', array(
		     'html' => "http://test.domain.com/test.html",
		     'image' => array(
			 'img/ico_1.png'=>'http://cdn.domain.com/img/ico_1.png',
			 'img/ico_2.png'=>"http://cdn.domain.com/img/ico_2.png",
		     ),
		     'css' =>array(
			 'css/main.css'=>"http://cdn.domain.com/css/main.css",
		     ),
		     'js' => array(
			 'js/pretty.js'=> "http://cdn.domain.com/js/pretty.js",
		     ),
		 ));
    }

    public function runGiven(&$world, $action, $arguments)
    {
	switch($action) {
	    case 'new publisher': {
		$world['publisher'] = new Publisher;
	    }
	    break;

	    default: {
		return $this->notImplemented($action);
	    }
	}
    }

    public function runWhen(&$world, $action, $arguments)
    {
	switch($action) {
	    case "set html path":
		$world['publisher']->setHtmlPath($arguments[0]);
		break;
	    case "set static path":
		$world['publisher']->setStaticPath($arguments[0]);
		break;
	    case "set static base url":
		$world['publisher']->setStaticBaseUrl($arguments[0]);
		break;
	    case "set html base url":
		$world["publisher"]->setHtmlBaseUrl($arguments[0]);
		break;
	    case "publish page":
		$world['result']=$world['publisher']->publishEntirePage($arguments[0],$arguments[1]);
		break;
	    case "publish image":
		$world['result']=$world['publisher']->publishImage($arguments[0],$arguments[1]);
		break;
	    case "publish js":
		$world['result']=$world['publisher']->publishJs($arguments[0],$arguments[1]);
		break;
	    case "publish css":
		$world['result']=$world['publisher']->publishCss($arguments[0],$arguments[1]);
		break;

	    default: {
		return $this->notImplemented($action);
	    }
	}
    }

    public function runThen(&$world, $action, $arguments)
    {
	switch($action) {
	    case 'return should be': {
		$this->assertEquals($arguments[0], $world['result']);
	    }
	    break;

	    default: {
		return $this->notImplemented($action);
	    }
	}
    }

}