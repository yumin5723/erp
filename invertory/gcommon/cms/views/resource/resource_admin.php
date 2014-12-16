<?php 
$this->pageTitle=Yii::t('cms','资源管理');
$this->pageHint=Yii::t('cms','在这里可以管理你的资源'); 
?>
<div id="site-content">
            <?php $this->renderPartial('/resource/nav'); ?>
        <div id="main-content-zone">
					<?php $this->widget('cmswidgets.ModelManageWidget',array('model_name'=>'Resource')); 
?>
                    </div>
            </div>
        </div>
        
</div>

