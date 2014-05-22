<?php
namespace console\controllers;
use Yii;
use gcommon\cms\components\CmsWorkerController;
use yii\console\Controller;
use gcommon\cms\models\Page;
class BuildpageController extends Controller{
    const INDEX_PAGE_ID = 68;
    /**
     * build index page per 5 mins;
     */
    public function actionIndex(){
        $page = Page::findOne(self::INDEX_PAGE_ID);
        $page->doPublish();
    }
}