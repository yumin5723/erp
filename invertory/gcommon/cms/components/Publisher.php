<?php
namespace gcommon\cms\components;

use Yii;
use yii\base\Component;
use yii\helpers\FileHelper;
class Publisher extends Component {
    /**
     * Default web accessible base path for storing published files
     */
    const DEFAULT_BASEPATH='pub';

    /**
     * @var array list of directories and files which should be excluded from
     * the publishing process.
     */
    public $excludeFiles=array('.svn','.gitignore');

    /**
     * @var integer the permission to be set for newly generated files.
     * Defaults to 0644, meaning the file is readable by all users, by only
     * writable by the process owner.
     */
    public $newFileMode=0644;

    /**
     * @var integer the permission to be set for newly generated directories.
     * Defaults to 0777, meaning the directory can by read and executed by the
     * process owner only, but can be read and executed by all user.
     */
    public $newDirMode=0755;

    /**
     * temporary directory for extracting files
     */
    public $temp_dir="/tmp";

    /**
     * template directory
     */
    public $template_dir;

    /**
     * domain => domain_base_directory
     *
     * @return
     */
    public $domains = array();

    /**
     * html dst path
     */
    protected $_htmlPath;

    /**
     * html base url
     */
    protected $_htmlBaseUrl;

    /**
     * css/js/image path
     * the seperate path will overrite this path
     */
    protected $_staticPath;

    /**
     * css/js/image base url
     */
    protected $_staticBaseUrl;

    /**
     * css path
     * if this path is empty, will use the $static_path
     */
    protected $_cssPath;

    /**
     * css base url
     */
    protected $_cssBaseUrl;

    /**
     * js path
     * if this path is empty, will use the $static_path
     */
    protected $_jsPath;

    /**
     * js base url
     */
    protected $_jsBaseUrl;

    /**
     * image path
     * if this path is empty, will use the $static_path
     */
    protected $_imagePath;

    /**
     * image base url
     */
    protected $_imageBaseUrl;

    /**
     * @var array published assets
     */
    protected $_published=array();

    function init() {

    }

    /**
     * @return  string the root directory storing the published html. Defaults
     * to "WebRoot".
     */
    public function getHtmlPath() {
        if ($this->_htmlPath===null) {
            $request=Yii::app()->getRequest();
            $this->setHtmlPath(dirname($request->getScriptFile()).DIRECTORY_SEPARATOR.self::DEFAULT_BASEPATH);
        }
        return $this->_htmlPath;
    }

    /**
     * Sets the root directory storing published html files.
     * @param string $value the root directory storing published html files
     *
     * @throws CException if the path is invalid
     */
    public function setHtmlPath($value) {
        if (($path=realpath($value))!==false && is_dir($path) && is_writable($path)) {
            $this->_htmlPath=$path;
        } else {
            throw new CException(Yii::t('xxx','Publiser.htmlPath "{path}" is invalid. Please make sure the direcotry exists and is writable by the web server process.',array('{path}'=>$value)));
        }
    }

    /**
     * @return string the base url that published html files can be accessed.
     * Note, the ending slashes are stripped off. Defaults to '/AppBaseUrl'.
     */
    public function getHtmlBaseUrl() {
        if ($this->_htmlBaseUrl===null) {
            $request = Yii::app()->getRequest();
            $this->setHtmlBaseUrl($request->getBaseUrl().'/'.self::DEFAULT_BASEPATH);
        }
        return $this->_htmlBaseUrl;
    }

    /**
     * @param string $value the base url that the published html files can be accessed
     */
    public function setHtmlBaseUrl($value) {
        $this->_htmlBaseUrl=rtrim($value,'/');
    }

    /**
     * @return string the root directory storing the published
     * static(css/js/image) fiels. Defaults to "WebRoot"
     */
    public function getStaticPath() {
        if ($this->_staticPath===null) {
            $request=Yii::app()->getRequest();
            $this->setStaticPath(dirname($request->getScriptFile()).DIRECTORY_SEPARATOR.self::DEFAULT_BASEPATH);
        }
        return $this->_staticPath;
    }

    /**
     * Sets the root direcotry storing the published statice files.
     * @param string $value the root direcotry storing published static files
     *
     * @throws CException if the path is invalid
     */
    public function setStaticPath($value) {
        if (($path=realpath($value))!==false && is_dir($path) && is_writable($path)) {
            $this->_staticPath=$path;
        } else {
            throw new \Exception(Yii::t('xxx','Publiser.staticPath "{path}" is invalid. Please make sure the direcotry exists and is writable by the web server process.',array('{path}'=>$value)));
        }
    }

    /**
     * @return string the base url that published static files can be accessed.
     * Note, the ending slashes are stripped off. Defaults to '/AppBaseUrl'.
     */
    public function getStaticBaseUrl() {
        if ($this->_staticBaseUrl===null) {
            $request = Yii::app()->getRequest();
            $this->setStaticBaseUrl($request->getBaseUrl().'/'.self::DEFAULT_BASEPATH);
        }
        return $this->_staticBaseUrl;
    }

    /**
     * @param string $value the base url that the published static files can be accessed
     */
    public function setStaticBaseUrl($value) {
        $this->_staticBaseUrl=rtrim($value,'/');
    }

    /**
     * @return string the root directory storing the published css files.
     * Defaults to "this->getStaticPath()".
     */
    public function getCssPath() {
        return $this->_cssPath!=null?$this->_cssPath:$this->getStaticPath();
    }

    /**
     * Sets the root direcotry storing published css files
     * @param string the root directory storing the published css files.
     */
    public function setCssPath($value) {
        if (($path=realpath($value))!==false && is_dir($path) && is_writable($path)) {
            $this->_cssPath=$path;
        } else {
            throw new CException(Yii::t('xxx','Publiser.cssPath "{path}" is invalid. Please make sure the direcotry exists and is writable by the web server process.',array('{path}'=>$value)));
        }
    }

    /**
     * @return string the base url that published css files can be accessed.
     * Note, the ending slashes are stripped off. Default to "this->getStaticBaseUrl()"
     */
    public function getCssBaseUrl() {
        return $this->_cssBaseUrl!=null?$this->_cssBaseUrl:$this->getStaticBaseUrl();
    }

    /**
     * @param string $value the base url that published css files can be accessed.
     */
    public function setCssBaseUrl($value) {
        $this->_cssBaseUrl=rtrim($value,'/');
    }

    /**
     * @return string the root directory storing the published javascript files.
     * Defaults to "this->getStaticPath()".
     */
    public function getJsPath() {
        return $this->_jsPath!=null?$this->_jsPath:$this->getStaticPath();
    }

    /**
     * Sets the root direcotry storing published javascript files
     * @param string the root directory storing the published javascript files.
     */
    public function setJsPath($value) {
        if (($path=realpath($value))!==false && is_dir($path) && is_writable($path)) {
            $this->_jsPath=$path;
        } else {
            throw new CException(Yii::t('xxx','Publiser.jsPath "{path}" is invalid. Please make sure the direcotry exists and is writable by the web server process.',array('{path}'=>$value)));
        }
    }

    /**
     * @return string the base url that published javascript files can be accessed.
     * Note, the ending slashes are stripped off. Default to "this->getStaticBaseUrl()"
     */
    public function getJsBaseUrl() {
        return $this->_jsBaseUrl!=null?$this->_jsBaseUrl:$this->getStaticBaseUrl();
    }

    /**
     * @param string $value the base url that published javascript files can be accessed.
     */
    public function setJsBaseUrl($value) {
        $this->_jsBaseUrl=rtrim($value,'/');
    }

    /**
     * @return string the root directory storing the published image files.
     * Defaults to "this->getStaticPath()".
     */
    public function getImagePath() {
        return $this->_imagePath!=null?$this->_imagePath:$this->getStaticPath();
    }

    /**
     * Sets the root direcotry storing published image files
     * @param string the root directory storing the published image files.
     */
    public function setImagePath($value) {
        if (($path=realpath($value))!==false && is_dir($path) && is_writable($path)) {
            $this->_imagePath=$path;
        } else {
            throw new CException(Yii::t('xxx','Publiser.imagePath "{path}" is invalid. Please make sure the direcotry exists and is writable by the web server process.',array('{path}'=>$value)));
        }
    }

    /**
     * @return string the base url that published image files can be accessed.
     * Note, the ending slashes are stripped off. Default to "this->getStaticBaseUrl()"
     */
    public function getImageBaseUrl() {
        return $this->_imageBaseUrl!=null?$this->_imageBaseUrl:$this->getStaticBaseUrl();
    }

    /**
     * @param string $value the base url that published image files can be accessed.
     */
    public function setImageBaseUrl($value) {
        $this->_imageBaseUrl=rtrim($value,'/');
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function clearCache() {
        $this->_published = array();
    }

    /**
     * publish css file to this->getCssPath(), keep the relative path info
     * if css contains some image files(url(a/a/c.jpg)), retrive the url
     * this->published and replace it
     * @param string $css_file the source css file to publish
     * @param string the destination file name where the css file should publish
     * to ,relative to $this->getCssPath()
     * @return the url published css file can accessed.
     */
    function publishCss($css_file, $dst) {
        // replace image path to urls
        $css_file = $this->processImageFilesInCss($css_file, $dst);

        // cp css file to dst
        $dst_file = $this->getCssPath().DIRECTORY_SEPARATOR.trim($dst,DIRECTORY_SEPARATOR);
        $dst_file = $this->copyFile($css_file, $dst_file);
        return $this->_published[$dst]=$this->getCssBaseUrl().substr($dst_file, strlen($this->getCssPath()));
    }

    /**
     * replace image file in css with there url
     * @param string $css_file source css to be parsed
     * @param string $dst the css file seats
     *
     * @return new css file path
     */
    protected function processImageFilesInCss($css_file, $dst) {
        $content = file_get_contents($css_file);
        if ($content === false) {
            return $css_file;
        }

        // find images in css
        $imgs = array();
        $re = '/(url\(\s*[\'"]?(\S*\.(?:jpe?g|gif|png)[\'"]?)\s*\))/i';
        if (preg_match_all($re, $content, $matches, PREG_SET_ORDER)) {
            $imgs = $matches;
        }

        if (empty($imgs)) {
            // has no image, no need to replace image path
            return $css_file;
        }

        // replace image path to url
        foreach ($imgs as $image) {
            // image[1] is url(../img/delete.png)
            // image[2] is ../img/delete.png
            if (strpos($image[2], "http://")===0 || strpos($image[2], "https://")===0) {
                continue;
            } elseif(strpos($image[2],"/")===0) {
                if (isset($this->_published[$image[2]])) {
                    $content = str_replace($image[1], "url(".$this->_published[$image].")", $content);
                }
                continue;
            } else {
                // calculate the relative path
                $i_path = dirname($dst);
                $pths = explode("/", $image[2]);
                foreach ($pths as $p) {
                    if ($p==".") {
                        continue;
                    } elseif($p=="..") {
                        $i_path = dirname($i_path);
                    } else {
                        $i_path = $i_path."/".$p;
                    }
                }
                $i_path=trim($i_path, ".");
                $i_path=trim($i_path, "/");
                if (isset($this->_published[$i_path])) {
                    $content = str_replace($image[1], "url(".$this->_published[$i_path].")", $content);
                }
            }
        }

        // save new file
        $tmp_file = tempnam($this->temp_dir, "tmp_css_");
        $handle = fopen($tmp_file, "w");
        fwrite($handle, $content);
        fclose($handle);

        return $tmp_file;
    }

    /**
     * save html content for domain
     *
     * @param $domain: domain configed in $this->domains
     * @param $html_path: relative path of saved html file
     * @param $html_content the html content string
     *
     * @return
     */
    public function saveDomainHtml($domain, $html_path, $html_content) {
        if (!isset($this->domains[$domain])) {
            throw new CException("Can not find publish directory for domain: ".$domain);
        }
        $dst_file = $this->domains[$domain].DIRECTORY_SEPARATOR.trim($html_path, DIRECTORY_SEPARATOR);
        $dir = dirname($dst_file);
        if (!is_dir($dir)) {
            mkdir($dir, $this->newDirMode, true);
        }
        Yii::Info("save html file to ".$dst_file);
        file_put_contents($dst_file, $html_content);
        @chmod($dst_file, $this->newFileMode);
        return $dst_file;
    }



    /**
     * publish javascript file to this->getJsPath(), keep the relative path info
     * from $dst.
     * @param string $js_file the source javascript file to publish
     * @param string the destination file name where the javascript should
     * publish to, relative to $this->getJsPath()
     * @return the url published javascript can accessed.
     */
    function publishJs($js_file, $dst) {
        // cp js file to dst
        $dst_file = $this->getJsPath().DIRECTORY_SEPARATOR.trim($dst,DIRECTORY_SEPARATOR);
        $dst_file = $this->copyFile($js_file, $dst_file);
        return $this->_published[$dst]=$this->getJsBaseUrl().substr($dst_file, strlen($this->getJsPath()));
    }

    /**
     * publish image file to this->getImagePath(), keep the relative path info
     * from $dst.
     * @param string $img_file the source image file to publish
     * @param string the destination file name where the image should
     * publish to, relative to $this->getImagePath()
     * @return the url published image can accessed.
     */
    function publishImage($img_file, $dst) {
        // cp image file to dst
        $dst_file = $this->getJsPath().DIRECTORY_SEPARATOR.trim($dst,DIRECTORY_SEPARATOR);
        $dst_file = $this->copyFile($img_file, $dst_file);
        return $this->_published[$dst]=$this->getImageBaseUrl().substr($dst_file, strlen($this->getImagePath()));
    }

    /**
     * copy from source file to dest file. if file already exists,
     * add --nnnnn-- beferefile extension.
     * nnn is md5 first 5 md5 hash value charactor of the file.
     * like simple.js -----> simple--abcde--.js
     *
     * @param $src_file:
     * @param $dst_file:
     *
     * @return string the real dest file
     */
    public function copyFile($src_file, $dst_file, $hash=true) {
        $dir = dirname($dst_file);
        if (!is_dir($dir)) {
            mkdir($dir, $this->newDirMode, true);
        }
        if ($hash) {
            $md5 = md5_file($src_file);
            $p = strrpos($dst_file, ".");
            $dst_file = substr($dst_file,0,$p)."--".substr($md5,0,5)."--".substr($dst_file, $p);
        }
        if (!is_file($dst_file)) {
            @copy($src_file, $dst_file);
        }
        @chmod($dst_file, $this->newFileMode);
        return $dst_file;
    }

    /**
     * publish entire page from rar file
     * the rar file must have dir/sample.html
     *
     *
     * @return array full files have published
     * @throws CException if can not find *.html
     */
    public function parseEntirePage($rar_file) {
        $this->clearCache();

        // extract the rar file
        $directory = $this->extractFiles($rar_file);
        // get sub directory contain file html
        $directory = rtrim($this->findDirectoryHasHtml($directory), DIRECTORY_SEPARATOR);
        $file_tree = $this->getFileTree($directory);
        $imgs = array();
        $jses = array();
        $csses = array();
        $html=null;
        foreach ($file_tree as $file) {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            if ($ext=="css") {
                $csses[]=$file;
            } elseif($ext=="html"&&is_null($html)) {
                $html=$file;
            } elseif($ext=="js") {
                $jses[]=$file;
            } elseif (in_array($ext, array('png','jpg','gif','jpeg'))) {
                // all other file as image
                $imgs[]=$file;
            }
        }
        $ret = array(
            "html"=>"",
            "image"=>array(),
            "css"=>array(),
            "js"=>array(),
        );
        // publish images first
        foreach ($imgs as $image) {
            $ret["image"][$image]=$this->publishImage($directory.DIRECTORY_SEPARATOR.$image,$image);
        }

        // publish javascript files
        foreach ($jses as $js) {
            $ret["js"][$js]=$this->publishJs($directory.DIRECTORY_SEPARATOR.$js,$js);
        }

        // publish css files
        foreach ($csses as $css) {
            $ret["css"][$css]=$this->publishCss($directory.DIRECTORY_SEPARATOR.$css,$css);
        }


        $ret['html'] = $this->processStaticFilesInHtml($directory.DIRECTORY_SEPARATOR.$html);
        return $ret;
    }

    /*
     * /\**
     *  * publish html to the destination.
     *  * replace image/css/js in the html
     *  * @param string $html_file source html file to publish
     *  * @param string $dst the path to publish relatived to $this->getHtmlPath().
     *  * @return the url for published html can be accessed.
     *  *\/
     * public function publishHtml($html_file, $dst, $domain=false) {
     *     // replace image path to urls
     *     /\* $html_file = $this->processStaticFilesInHtml($html_file); *\/
     *
     *     // cp html file to dst
     *     if ($domain && isset($this->domains[$domain])) {
     *         $dst_file = $this->domains[$domain].DIRECTORY_SEPARATOR.trim($dst,DIRECTORY_SEPARATOR);
     *     } else {
     *         $dst_file = $this->getHtmlPath().DIRECTORY_SEPARATOR.trim($dst,DIRECTORY_SEPARATOR);
     *     }
     *     $dst_file = $this->copyFile($html_file, $dst_file, false);
     *     if ($domain && isset($this->domains[$domain])) {
     *         return $this->_published[$dst]='http://'.$domain.DIRECTORY_SEPARATOR.substr($dst_file, strlen($this->getHtmlPath()));
     *     } else {
     *         return $this->_published[$dst]=$this->getHtmlBaseUrl().substr($dst_file, strlen($this->getHtmlPath()));
     *     }
     * }
     */

    /**
     * replace all static files in html with there url
     *
     * @param $html_file:
     *
     * @return string html
     */
    public function processStaticFilesInHtml($html_file) {
        // include_once(Yii::getPathOfAlias("gcommon.lib")."/simple_html_dom.php");
        include_once(dirname(dirname(__DIR__)) . '/lib/simple_html_dom.php');
        $html=file_get_html($html_file);

        // replace image src
        foreach ($html->find('img') as $element) {
            if (isset($this->_published[$element->src])) {
                $element->src=$this->_published[$element->src];
            }
        }

        // replace css files
        foreach ($html->find('link') as $element) {
            if ($element->type=="text/css" && isset($this->_published[$element->href])) {
                $element->href=$this->_published[$element->href];
            }
        }

        // replace js files
        foreach ($html->find('script') as $element) {
            if ($element->type=="text/javascript" && isset($this->_published[$element->src])) {
                $element->src=$this->_published[$element->src];
            }
        }

        /*
         * // save new file
         * $tmp_file = tempnam($this->temp_dir, "tmp_html_");
         * $handle = fopen($tmp_file, "w");
         * fwrite($handle, $html->__toString());
         * fclose($handle);
         *
         * return $tmp_file;
         */
        $config = array(
                'indent' => true,
                'indent-spaces' => 2,
                'wrap'=>0,
                'doctype'=>'user',
                "drop-empty-elements"=>false,
            );
        $result = $html->__toString();
        unset($html);
        gc_collect_cycles();
        return @tidy_repair_string($result,$config,"utf8");
    }



    /**
     * get all files tree from directory
     *
     * @param $dir:
     *
     * @return files in directory
     */
    protected function getFileTree($dir) {
        $dir = rtrim($dir, DIRECTORY_SEPARATOR);
        $tree=array();
        $folders = array($dir);
        while (!empty($folders)) {
            $d = array_pop($folders);
            $d_handler = opendir($d);
            while ($file=readdir($d_handler)) {
                $real_file=$d.DIRECTORY_SEPARATOR.$file;
                if (is_dir($real_file) && $file!="." && $file!="..") {
                    $folders[] = $real_file;
                } elseif(is_file($real_file)) {
                    $tree[] = substr($real_file, strlen($dir)+1);
                }
            }
        }
        return $tree;
    }


    /**
     * Search sub directory for html file
     */
    protected function findDirectoryHasHtml($dir) {
        while (true) {
            $list = glob($dir."/*.html");
            if (!empty($list)) {
                break;
            } else {
                $sub_dirs = glob($dir."/*",GLOB_ONLYDIR);
                if (empty($sub_dirs)) {
                    break;
                }
                foreach ($sub_dirs as $l) {
                    if (is_dir($l)) {
                        $dir = $l;
                        break;
                    }
                }
            }
        }
        if (empty($list)) {
            throw new CException("Can not find html files from rar");
        }
        return $dir;
    }

    /**
     * function_description
     *
     *
     * @return string $path the template file path contain extracted files
     * @throws CException when can not open as rar file
     */
    protected function extractFiles($compressed_file) {
        //generate temporary random directory
        $dir = sprintf($this->temp_dir. "/"."%x", crc32(microtime()));
        mkdir($dir, 0755);
        //extract file
        $rar_file=rar_open($compressed_file);
        if (!$rar_file) {
            throw new CException("Can not open rar file,". $compressed_file);
        }
        $entries=rar_list($rar_file);
        foreach ($entries as $entry) {
            $entry->extract($dir);
        }
        rar_close($rar_file);
        return $dir;
    }


}