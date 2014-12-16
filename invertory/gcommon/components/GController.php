<?php
namespace gcommon\components;
use Yii;
use yii\web\Controller;
/**
 * For controller use twig template engine.
 */
class GController extends Controller {
    public $layout = "@backend/views/layouts/top";


    public function behaviors()
    {
        return [
            'access' => [
                'class' => \backend\components\filters\AccessControl::className(),
            ],
        ];
    }

    /**
     * function_description
     *
     * @param $viewName:
     *
     * @return
     */
    // public function getViewFile($viewName) {
    //     $viewName = $this->getId()."/".$viewName.Yii::app()->getViewRenderer()->fileExtension;
    //     if (($module=$this->getModule())!==null) {
    //         $viewName = "@".$module->getId().'/'.$viewName;
    //     }
    //     return $viewName;
    // }

    // public function getBaseView() {
    //     $module = $this->getModule();
    //     if (isset($module->baseView)) {
    //         return $module->baseView;
    //     } else {
    //         return "layouts/base".Yii::app()->getViewRenderer()->fileExtension;
    //     }
    // }

    // /**
    //  * function_description
    //  *
    //  *
    //  * @return
    //  */
    // public function importCmsViews() {
    //     Yii::$app->viewRenderer->getTwig()->getLoader()->addPath(Yii::$app->getModule('cms')->getViewPath(),"cms");
    // }


}