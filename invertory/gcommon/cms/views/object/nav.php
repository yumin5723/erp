<?php $this->renderPartial('/layouts/cmstop'); ?>
<div id="left-sidebar">
<?php
$this->widget('zii.widgets.CMenu',array(
	'encodeLabel'=>false,
	'activateItems'=>true,
	'activeCssClass'=>'list_active',
	'items'=>array(


		//Content Menu 
		array(
			'label'=>'<span id="menu_content" class="micon"></span>'.Yii::t('cms','文章'),  
			'url'=>'javascript:void(0);','linkOptions'=>array('class'=>'menu_1' ), 
			'itemOptions'=>array('id'=>'menu_1'), 
			'items'=>array(
        array(
                'label'=>Yii::t('cms','创建文章'), 
                'url'=>array('object/create'),
                'active'=>Yii::app()->controller->id=='object' && Yii::app()->controller->action->id=='create'
          ),
				array(
					'label'=>Yii::t('cms','文章管理'), 
					'url'=>array('object/admin/type/0'),
					// 'visible'=>user()->isAdmin ? true : false,
					'active'=> ((Yii::app()->controller->id=='object') && (in_array(Yii::app()->controller->action->id,array('update','view','admin','index'))) ? true : false)
					),
          array(
                'label'=>Yii::t('cms','历史记录'), 
                'url'=>array('object/history'),
                'active'=>Yii::app()->controller->id=='object' && Yii::app()->controller->action->id=='history'
                ),
				)
			),
    ),
));
?>
</div>