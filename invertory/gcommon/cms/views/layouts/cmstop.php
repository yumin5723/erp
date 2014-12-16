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
<script type="text/javascript">
        $(document).ready(function(){
                var cms_help = $("#cms-help");
                if (cms_help.length > 0) {
                    cms_help.hide();
                    var html_pre = '<button type="button" class="btn" data-toggle="collapse" data-target="#cms-help-collapse"><i class="icon-question-sign"></i></button><div id="cms-help-collapse" class="collapse ">';
                    var html_post = "</div>";
                    $("#main-content-zone").prepend(html_pre+cms_help.html()+html_post);
                }
            });
</script>