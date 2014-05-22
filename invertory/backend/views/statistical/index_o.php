<?php
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;

$this->title = '运营相关统计';
$sidebar_name = '运营相关统计';
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
<h1>运营相关统计</h1>
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
                    'attribute' => 'register_nums',
                    'label'=>'注册用户数(人)',
                ],
                [
                    'attribute' => 'lottery_nums',
                    'label'=>'抽奖人数(人)',
                ],
                [
                    'attribute' => 'auction_nums',
                    'label'=>'竞拍人数(人)',
                ],
                [
                    'attribute' => 'posts',
                    'label'=>'论坛发帖量',
                ],
                [
                    'attribute' => 'ask_nums',
                    'label'=>'知道参与人数(人)',
                ],
                [
                    'attribute' => 'health_nums',
                    'label'=>'医疗参与人数(人)',
                ],
                [
                    'attribute' => 'create_date',
                    'label'=>'统计日期',
                ],
                
        ],
]);
?>
</div>