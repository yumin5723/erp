<?php
$this->pageTitle = Yii::t('cms', '内容管理');
$this->pageHint = Yii::t('cms', '在这里你可以管理所有你的更改记录');
?>
<div id="site-content">
            <?php $this->renderPartial('/object/nav'); ?>
        <div id="main-content-zone">
                        <?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'object-grid',
    'dataProvider'=>$model->search(),
    'filter' => $model,
    'summaryText' => Yii::t('cms', 'Displaying') . ' {start} - {end} ' . Yii::t('cms', 'in') . ' {count} ' . Yii::t('cms', 'results') ,
    'pager' => array(
        'header' => Yii::t('cms', '到这一页:') ,
        'nextPageLabel' => Yii::t('cms', '下一页') ,
        'prevPageLabel' => Yii::t('cms', '上一页') ,
        'firstPageLabel' => Yii::t('cms', '首页') ,
        'lastPageLabel' => Yii::t('cms', '末页') ,
    ) ,
    'columns' => array(
        array(
            'name' => 'id',
            'type' => 'raw',
            'htmlOptions' => array(
                'class' => 'gridmaxwidth'
            ) ,
            'value' => '$data->id',
        ) ,
        array(
            'name' => 'object_id',
            'type' => 'raw',
            'htmlOptions' => array(
                'class' => 'gridmaxwidth'
            ) ,
            'value' => '$data->object_id',
        ) ,
        array(
            'name' => 'object_title',
            'type' => 'raw',
            'htmlOptions' => array(
                'class' => 'gridLeft'
            ) ,
            'value' => '$data->object_title',
        ) ,
        array(
            'name' => 'object_author_name',
            'type' => 'raw',
            'htmlOptions' => array(
                'class' => 'gridLeft'
            ) ,
            'value' => '$data->object_author_name',
        ) ,
        array(
            'name' => 'modified',
            'type' => 'raw',
            'htmlOptions' => array(
                'class' => 'gridLeft'
            ) ,
            'value' => '$data->modified',
        ) ,
        array(
            'class' => 'CButtonColumn',
            'template' => '{view}',
            'buttons' => array(
                'view' => array(
                    'label' => Yii::t('cms', '查看') ,
                    'imageUrl' => false,
                    'url' => 'Yii::app()->createUrl("/cms/object/bakview", array("id"=>$data->id))',
                ) ,
            ) ,
        ) ,
        array(
            'class' => 'CButtonColumn',
            'template' => '{update}',
            'buttons' => array(
                'update' => array(
                    'label' => Yii::t('cms', '重新应用') ,
                    'imageUrl' => false,
                    'url' => 'Yii::app()->createUrl("/cms/object/update", array("id"=>$data->object_id,"history_id"=>$data->id))',
                ) ,
            ) ,
        ) ,
        array(
            'class' => 'CButtonColumn',
            'template' => '{delete}',
            'buttons' => array(
                'delete' => array(
                    'label' => Yii::t('cms', '删除') ,
                    'imageUrl' => false,
                    'url' => 'Yii::app()->createUrl("/cms/object/bakdelete", array("id"=>$data->id))',
                ) ,
            ) ,
        ) ,
    ) ,
)); ?>
                    </div>
            </div>
        </div>
        
</div>
