<?php
namespace common\extensions\page;
use yii\widgets\LinkPager;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\base\Widget;
use yii\data\Pagination;

class CPageLink extends LinkPager{
    public $options = ['class' => 'pagination pull-right'];
}