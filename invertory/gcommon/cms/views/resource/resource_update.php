<?php 
$this->pageTitle=Yii::t('cms','修改资源');
$this->pageHint=Yii::t('cms','在这里你可以更新为当前的资源信息'); 
?>
<div id="site-content">
            <?php $this->renderPartial('/resource/nav'); ?>
        <div id="main-content-zone">
					<?php 
$this->widget('cmswidgets.resource.ResourceUpdateWidget',array()); 
?>
                    </div>
            </div>
        </div>
        
</div>


