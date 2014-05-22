<?php
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;

$this->title = '电商相关统计';
$sidebar_name = '电商相关统计';
$sidebars = [
        [
            'name' => '运营相关统计',
            'icon' => 'tasks',
            'url' => '/statistical/operations',
        ],
        [
            'name' => '电商相关统计',
            'icon' => 'tasks',
            'url' => '/statistical/electricity',
        ],
    ];
include(__DIR__."/../layouts/base_sidebar.php");

?>
<div class="content">
<h1>电商相关统计</h1>
<?php
echo GridView::widget([
        'dataProvider' => $data,
        'id' => 'log-panel-detailed-grid',
        'options' => ['class' => 'detail-grid-view'],
        'columns' => [
                [
                    'attribute' => 'id',
                ],
                [
                    'attribute' => 'statistics_date',
                    'label'=>'统计日期',
                ],
                [
                    'attribute' => 'mobile_sales',
                    'label'=>'移动端销售额(元)',
                ],
                [
                    'attribute' => 'buy_nums',
                    'label'=>'移动端购买人数(人)',
                ],
                [
                    'attribute' => 'visitors',
                    'label'=>'移动端访问人数(人)',
                ],
                [
                    'attribute' => 'dau',
                    'label'=>'日访问人数(人)',
                ],
                [
                    'attribute' => 'second_retain',
                    'label'=>'次日留存(人)',
                ],
                [
                    'attribute' => 'week_retain',
                    'label'=>'七日留存(人)',
                ],
                [
                    'attribute' => 'increasing',
                    'label'=>'日新增用户(人)',
                ],
                [
                    'attribute' => 'create_date',
                    'label'=>'记录日期',
                ],
                
        ],
]);
?>
</div>