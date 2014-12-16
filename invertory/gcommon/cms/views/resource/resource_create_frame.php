<?php

        $cs=Yii::app()->clientScript;
        $cssCoreUrl = $cs->getCoreScriptUrl();
        $cs->registerCoreScript('jquery');
        $cs->registerCoreScript('jquery.ui');
        $cs->registerCssFile($cssCoreUrl . '/jui/css/base/jquery-ui.css');
                

?>
<script type="text/javascript" src="<?php echo $this->module->assetsUrl; ?>/js/backend.js"></script>
<script type="text/javascript" src="<?php echo $this->module->assetsUrl; ?>/js/jquery.prettyPhoto.js"></script>
<script type="text/javascript" src="<?php echo $this->module->assetsUrl; ?>/js/jquery.ui.position.js"></script>
<script type="text/javascript" src="<?php echo $this->module->assetsUrl; ?>/js/jquery.contextMenu.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->module->assetsUrl; ?>/css/screen.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->module->assetsUrl; ?>/css/main.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->module->assetsUrl; ?>/css/form.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->module->assetsUrl; ?>/css/prettyPhoto.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->module->assetsUrl; ?>/css/jquery.contextMenu.css" />
<script type="text/javascript" charset="utf-8">
        $(document).ready(function(){
             $("a[rel^='prettyPhoto']").prettyPhoto({show_title: true,social_tools: '',deeplinking: false});
        });
</script>
        <div id="page" class="container">                        
					<?php $this->widget('cmswidgets.resource.ResourceCreateWidget',array()); 
					?>
        </div>


