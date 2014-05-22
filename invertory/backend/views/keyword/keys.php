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
$sidebar_name = '管理敏感词';
$sidebars = [
        [
            'name' => '敏感词',
            'icon' => 'tasks',
            'url' => '/keyword/list',
        ],
        [
            'name' => '添加敏感词',
            'icon' => 'tasks',
            'url' => '/keyword/create',
        ],
    ];
$this->title = '敏感词';
?>
<?php include(__DIR__."/../layouts/base_sidebar.php");?>
<div class="content">
<h1>管理员信息</h1>
<div class="detail-grid-view" id="log-panel-detailed-grid">
    <div class="summary">Showing <b>1-1</b> of <b><?php echo count($data)?></b> item.</div>
        <table class="table table-striped table-bordered">
            <thead>
                <tr><th>Id</th><th>敏感词</th><th>操作</th></tr>
            </thead>
            <tbody>
                <?php foreach ($data as $key => $value) { ?>
                        <tr data-key="1"><td><?php echo $key ?></td><td><?php echo $value?></td><td><a href="/keyword/delete?word=<?php echo $value ?>" class="delete" onclick="return confirm(&quot;是否将此用户信息删除?&quot;)">delete</a></td></tr>
                <?php } ?>
            </tbody>
        </table>
<ul class="pagination"><li class="prev disabled"><span>«</span></li>
<li class="active"><a data-page="0" href="/keyword/admin?page=1">1</a></li>
<li class="next disabled"><span>»</span></li></ul></div>
</div>