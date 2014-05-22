<?php
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\jui\DatePicker;
use yii\data\ArrayDataProvider;
/**
* @var yii\web\View $this
* @var yii\gii\generators\crud\Generator $generator
*/
$sidebar_name = '管理关键词';
$sidebars = [
        [
            'name' => '管理关键词',
            'icon' => 'tasks',
            'url' => '/species/admin',
        ],
        [
            'name' => '创建关键词',
            'icon' => 'tasks',
            'url' => '/species/create',
        ],
    ];
$this->title = '管理管理员';
?>
<?php include(__DIR__."/../layouts/base_sidebar.php");?>
<div class="content">
<h2>管理关键词</h2>
<?= yii\jui\DatePicker::widget(['name' => 'spe_id','attribute' => 'spe_id', 
'model'=>$model,
'clientOptions' => 
['dateFormat' => 'yy-mm-dd hh:ii:ss',
'changeMonth'=>true,
'changeYear'=>true,
'yearRange'=>'-1:+25',   ]]) ?>
<?php
echo GridView::widget([
        'dataProvider' => $model->getAllData(),
        'id' => 'log-panel-detailed-grid',
        'options' => ['class' => 'detail-grid-view'],
        'filterModel'=>$model,
        'columns' => [
                [
                    'attribute' => 'id',
                ],
                [
                    'attribute' => 'spe_id',
                    'value'=>function($data){
                    },
                ],
                [
                    'attribute' => 'spe_title',
                    'value'=>function($data){
                        if($data['id']>5){
                            return 'dd';
                        }
                        return 'ss';
                    },
                ],
                [
                    'attribute' => '操作',
                    'value'=>function($data){
                        return '<a href="/species/update/'.$data['id'].'">update</a>///<a class="delete" href="/species/delete/'.$data['id'].'">delete</a>';
                  },
                    'format' => 'html',
                ],
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'name'=>'selection',
                ],
        ],
]);
?>
</div>