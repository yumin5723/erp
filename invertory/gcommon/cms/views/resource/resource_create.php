<?php 
$this->pageTitle=Yii::t('cms','添加资源');
$this->pageHint=Yii::t('cms','这里可以添加新的资源'); 
?>
<div id="site-content">
            <?php $this->renderPartial('/resource/nav'); ?>
        <div id="main-content-zone">
      
                        
					<?php $this->widget('cmswidgets.resource.ResourceCreateWidget',array()); 
					?>
                    </div>
            </div>
        </div>
        
</div>


