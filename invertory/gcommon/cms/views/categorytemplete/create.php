<?php 
$this->pageTitle=Yii::t('cms','上传新的模板');
?>
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
                                <?php echo $form->labelEx($model,'name'); ?>
                                <?php echo $form->textField($model,'name'); ?>
                                <?php echo $form->error($model,'name'); ?>
                        </div>
                        <div class="row">
                                <?php echo $form->labelEx($model,'upload',array()); ?>
                                <?php
                                    echo $form->fileField($model,'upload') ;
                                ?>
                                <?php echo $form->error($model,'upload'); ?>
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


