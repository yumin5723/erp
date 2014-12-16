<?php 
    $this->pageTitle=Yii::t('cms','Object details'); 
?>
<script type="text/javascript" src="<?php echo $this->module->assetsUrl; ?>/js/ckeditor/ckeditor.js"></script>
<div id="site-content">
            <?php $this->renderPartial('/object/nav'); ?>
        <div id="main-content-zone">
                        <?php $this->widget('zii.widgets.CDetailView', array(
    'data'=>$model,
    'attributes'=>array(
        'id',
        'object_id',
        'object_author_name',
        'object_title',
        'object_content',
        'tags',
        'created',
        'modified',
    ),
)); ?>
                    </div>
            </div>
        </div>
        
</div>

