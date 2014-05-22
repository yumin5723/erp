<?php
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
/**
* @var yii\web\View $this
* @var yii\gii\generators\crud\Generator $generator
*/
$sidebar_name = '管理监控点';
$sidebars = [
        [
            'name' => '管理监控点',
            'icon' => 'tasks',
            'url' => '/monitor/admin',
        ],
        [
            'name' => '创建监控点',
            'icon' => 'tasks',
            'url' => '/monitor/create',
        ],
    ];
$this->title = '管理管理员';
?>
<?php include(__DIR__."/../layouts/base_sidebar.php");?>
<div class="content">
<h1>管理员信息</h1>
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
                    'attribute' => 'desc',
                ],
                [
                    'attribute' => 'create_time',
                    'value'=>function($data){
                        return date("Y-m-d",$data['create_time']);
                    },
                ],
                [
                    'attribute' => 'update_time',
                    'value'=>function($data){
                        return date("Y-m-d",$data['create_time']);
                    },
                ],
                [
                    'attribute' => '操作',
                    'value'=>function($data){
                        return '<a href="/monitor/update/'.$data['id'].'">update</a>';
                        // return '<a href="/manager/update/'.$data['id'].'">update</a>///<a class="delete" href="/manager/delete/'.$data['id'].'" class="ddd" >delete</a>';
                        // $r= Html::a('delete',"/manager/delete",['class'=>'return confirm(\'是否将此留言信息删除?\')','id'=>'delete','onclick'=>'return yesno()']);
                        // return $r;
                        // return Html::a('PHssP ' , ['phpindfo'], ['class' => 'delete']);
                        // return Html::encode('<a href="/manager/update/'.$data['id'].'">update</a>///<a class="delete" href="/manager/delete/'.$data['id'].'" onClick="return confirm(\"是否将此留言信息删除?\")" >delete</a>');
                    },
                    'format' => 'html',
                ],
        ],
]);
?>
</div>