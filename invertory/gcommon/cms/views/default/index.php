<?php 
$this->renderPartial("/layouts/menu",array());
?>
<?php
$this->pageTitle=Yii::app()->name;
?>
<p><?php echo Yii::t('cms','What do you want to do today?'); ?></p>
<div>
<ul class="shortcut-buttons-set">
<li>
<a href="/cms/object/create" class="shortcut-button">
<span>
<img alt="icon" src="<?php echo $this->module->assetsUrl; ?>/images/richtext.png" height="48" width="48"><br />
<?php echo Yii::t('cms','创建文章');?>
</span></a></li>

<li>
<a href="/cms/page/create" class="shortcut-button">
<span>
<img alt="icon" src="<?php echo $this->module->assetsUrl; ?>/images/paper.png"><br />
<?php echo Yii::t('cms','创建页');?>
</span></a></li>


<li>
<a href="/cms/resource/create" class="shortcut-button">
<span>
<img alt="icon" src="<?php echo $this->module->assetsUrl; ?>/images/upload_file.png"  width="48px" height="48px"><br />
<?php echo Yii::t('cms','上传文件');?>
</span></a></li>


</ul>
<div style="clear:both"></div>
</div>