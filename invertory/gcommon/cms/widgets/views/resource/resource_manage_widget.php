<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'resource-grid',
    'dataProvider'=>$model->search(),
    'filter'=>$model,
     'summaryText'=>Yii::t('cms','Displaying').' {start} - {end} '.Yii::t('cms','in'). ' {count} '.Yii::t('cms','results'),
    'pager' => array(
        'header'=>Yii::t('cms','Go to page:'),
        'nextPageLabel' =>  Yii::t('cms','Next'),
        'prevPageLabel' =>  Yii::t('cms','previous'),
        'firstPageLabel' =>  Yii::t('cms','First'),
        'lastPageLabel' =>  Yii::t('cms','Last'),
    ),
    'columns'=>array(
        array('name'=>'resource_id',
            'type'=>'raw',
            'htmlOptions'=>array('class'=>'gridmaxwidth'),
            'value'=>'$data->resource_id',
            ),
        array(
            'name'=>'resource_name',
            'type'=>'raw',
            'htmlOptions'=>array('class'=>'gridLeft'),
            'value'=>'CHtml::link($data->resource_name,array("/view","id"=>$data->resource_id))',
            ),
        array(
            'name'=>'resource_path',
            'type'=>'raw',
            'htmlOptions'=>array('class'=>'gridLeft'),
            'value'=>'GxcHelpers::renderTextBoxResourcePath($data)',
            ),
        array(
            'name'=>'resource_type',
            'type'=>'raw',
            'htmlOptions'=>array('class'=>'gridLeft gridmaxwidth'),
            'value'=>'$data->resource_type',
            ),
        array(
            'name'=>'created',
            'type'=>'raw',
            'htmlOptions'=>array('class'=>'gridLeft gridmaxwidth'),
            'value'=>'$data->created',
            ),
        array(
            'header'=>'',           
            'type'=>'raw',
            'htmlOptions'=>array('class'=>'button-column'),
            'value'=>'GxcHelpers::renderLinkPreviewResource($data)',
            ), 
        array
        (
            'class'=>'CButtonColumn',
            'template'=>'{update}',
            'buttons'=>array
            (
            'update' => array
            (
                'label'=>Yii::t('cms','修改'),
                'imageUrl'=>false,
                'value'=>'CHtml::link($data->resource_id,array())',
            ),
            ),
        ),
        array
        (
            'class'=>'CButtonColumn',
            'template'=>'{delete}',
            'buttons'=>array
            (
            'delete' => array
            (
                'label'=>Yii::t('cms','删除'),
                'imageUrl'=>false,
            ),

            ),
        ),
    ),
)); ?>
<?php
//in your view where you want to include JavaScripts
$cs = Yii::app()->getClientScript();  
$cs->registerScript(
  'resource-field-handle',
  '
        function selectAllText(textbox) {
            textbox.focus();
            textbox.select();           
        }
        $(".pathResource").click(function() { selectAllText(jQuery(this)) });
  ',
  CClientScript::POS_END
);
?>