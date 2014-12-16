<?php $this->renderPartial('/layouts/cmstop'); ?>
<div id="left-sidebar">
<?php
$this->widget('zii.widgets.CMenu',array(
  'encodeLabel'=>false,
  'activateItems'=>true,
  'activeCssClass'=>'list_active',
  'items'=>array(


    array(
          'label'=>'<span id="menu_resource" class="micon"></span>'.Yii::t('cms','上传'), 
          'url'=>'javascript:void(0);',
          'linkOptions'=>array('id'=>'menu_4','class'=>'menu_4'), 
          'itemOptions'=>array('id'=>'menu_4'), 
          'items'=>array(
            array(
              'label'=>Yii::t('cms','上传文件'), 
              'url'=>array('resource/create'),
              'active'=>Yii::app()->controller->id=='resource' && Yii::app()->controller->action->id=='create'              
            ),
            array(
              'label'=>Yii::t('cms','管理文件'), 
              'url'=>array('resource/admin'),
              'active'=> ((Yii::app()->controller->id=='resource') && (in_array(Yii::app()->controller->action->id,array('update','view','admin','index')))) ? true : false
            )
          )
      ),
    ),
));
?>
</div>