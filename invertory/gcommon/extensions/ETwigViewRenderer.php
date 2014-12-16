<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace gcommon\extensions;
use yii\twig;
use yii\twig\ViewRenderer;
use Twig_Loader_String;
/**
 * TwigViewRenderer allows you to use Twig templates in views.
 *
 * @property array $lexerOptions @see self::$lexerOptions. This property is write-only.
 *
 * @author Alexander Makarov <sam@rmcreative.ru>
 * @since 2.0
 */
class ETwigViewRenderer extends ViewRenderer
{

    /**
     * Renders a view file.
     *
     * This method is invoked by [[View]] whenever it tries to render a view.
     * Child classes must implement this method to render the given view file.
     *
     * @param View   $view   the view object used for rendering the file.
     * @param string $file   the view file.
     * @param array  $params the parameters to be passed to the view file.
     *
     * @return string the rendering result
     */
    public function render($view, $file, $params)
    {
        $this->twig->addGlobal('this', $view);
        $this->twig->setLoader(new Twig_Loader_String());
        return $this->twig->render($file, $params);
    }
}
