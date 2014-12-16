<?php 
$this->pageTitle=Yii::t('cms','修改模板');
?>
<script src="<?php echo $this->module->assetsUrl; ?>/js/ckeditor/ckeditor.js" type="text/javascript"></script>
<div id="site-content">
    <?php $this->renderPartial('/layouts/menu'); ?>
    <div id="main-content-zone">
        <?php if(isset($this->menu)) :?>
        <?php if(count($this->menu) >0 ): ?>
        <div class="header-info">
        <?php                                       
        $this->widget('zii.widgets.CMenu', array(
              'items'=>$this->menu,
        ));                                       
        ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
        <div class="page-content"> 
            <div id="inner" style="width:100%;float:left;">
                    <div class="form">
                        <?php $form=$this->beginWidget('CActiveForm', array(
                                'id'=>'taxonomy-form',
                                'htmlOptions'=>array('enctype'=>'multipart/form-data'),    
                                )); 
                        ?>

                        <?php echo $form->errorSummary($model); ?>
                        <?php               
                            $this->renderPartial('/layouts/notify',array());         
                        ?>   

                        <div class="row">
                                <?php echo $form->labelEx($model,'content'); ?>
                                <?php echo $form->textArea($model,'content',array('id'=>'ckeditor_content')); ?>
                                <?php echo $form->error($model,'content'); ?>
                        </div>
                        <div class="row buttons">
                                <?php echo CHtml::submitButton(Yii::t('cms','保存'),array('class'=>'bebutton')); ?>
                        </div>
                    <?php $this->endWidget(); ?>
                </div><!-- form -->
            </div>
        </div>
    </div>
                
</div>
<script>
CKEDITOR.replace( 'ckeditor_content', {
        toolbar: 'Full',
        width:'820',
        startupMode:'source',
    });
</script>

