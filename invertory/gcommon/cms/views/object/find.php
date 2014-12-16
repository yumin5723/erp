<?php 
    $this->pageTitle = Yii::t('cms', '内容管理');
    $this->pageHint = Yii::t('cms', '在这里你可以管理所有你的网站内容');
?>
<div id="site-content">
        <?php $this->renderPartial('/object/nav'); ?>
        <div id="main-content-zone">
                        <?php               
                            $this->renderPartial('/layouts/notify',array());         
                        ?>  
                        <br />
                        <h2>当前分类:<?php echo $term->name; ?></h2>
                        <?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'object-grid',
    'dataProvider' => $result,
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
            'name' => 'object_id',
            'type' => 'raw',
            'htmlOptions' => array(
                'class' => 'gridmaxwidth'
            ) ,
            'value' => '$data->object_id',
        ) ,
        array(
            'name' => 'object_name',
            'type' => 'raw',
            'htmlOptions' => array(
                'class' => 'gridLeft'
            ) ,
            'value' => 'CHtml::link($data->object_name,array("object/view","id"=>$data->object_id))',
        ) ,
        array(
            'name' => 'url',
            'type' => 'raw',
            'htmlOptions' => array(
                'class' => 'gridLeft'
            ) ,
            'value' =>'Object::convertObjectUrl($data->url)',
        ) ,
        array(
            'name' => 'object_date',
            'type' => 'raw',
            'htmlOptions' => array(
                'class' => 'gridLeft'
            ) ,
        ) ,
        array(
            'name' => 'object_status',
            'type' => 'raw',
            'htmlOptions' => array(
                'class' => 'gridLeft gridmaxwidth'
            ) ,
            'value' => 'Object::convertObjectStatus($data->object_status)',
        ) ,
        array(
                'type'=>'raw',
                'htmlOptions'=>array( 'class'=>'gridLeft'),
                'value'=>'CHtml::link("预览",Yii::app()->createUrl("/cms/object/preview",array("id"=>$data->object_id)),
                    array( "target"=>"_blank"))',
            ),
        array(
                'type'=>'raw',
                'htmlOptions'=>array( 'class'=>'gridLeft' ),
                'value'=>'CHtml::link($data->object_status == 0 ? "" : ($data->object_status == 1 ? "发布" : "重新发布"),Yii::app()->createUrl("/cms/object/publish",array("id"=>$data->object_id)))',
            ),
        array(
            'class' => 'CButtonColumn',
            'template' => '{update}',
            'buttons' => array(
                'update' => array(
                    'label' => Yii::t('cms', '修改') ,
                    'imageUrl' => false,
                    // 'url' => 'Yii::app()->createUrl("/cms/object/update", array("id"=>$data->object_id))',
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
                ) ,
            ) ,
        ) ,
    ) ,
)); ?>
                    </div>
            </div>
        </div>
        
</div>
 
