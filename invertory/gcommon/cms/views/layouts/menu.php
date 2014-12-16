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
<div id="left-sidebar">
<?php
$this->widget('zii.widgets.CMenu',array(
	'encodeLabel'=>false,
	'activateItems'=>true,
	'activeCssClass'=>'list_active',
	'items'=>array(

		//Dasboard Menu 
		array(
			'label'=>'<span id="menu_dashboard" class="micon"></span>'.Yii::t('cms','常用功能'), 
			'url'=>array('default/index') ,'linkOptions'=>array('class'=>'menu_0'),
			'active'=> ((Yii::app()->controller->id=='default') && (in_array(Yii::app()->controller->action->id,array('index'))) ? true : false)
			),                               

		//Content Menu 
		array(
			'label'=>'<span id="menu_content" class="micon"></span>'.Yii::t('cms','文章'),  
			'url'=>'javascript:void(0);','linkOptions'=>array('class'=>'menu_1' ), 
			'itemOptions'=>array('id'=>'menu_1'), 
			'items'=>array(
        array(
                'label'=>Yii::t('cms','创建文章'), 
                'url'=>array('object/create'),
                'active'=>Yii::app()->controller->id=='object' && Yii::app()->controller->action->id=='create'
          ),
				array(
					'label'=>Yii::t('cms','文章管理'), 
					'url'=>array('object/admin/type/0'),
					// 'visible'=>user()->isAdmin ? true : false,
					'active'=> ((Yii::app()->controller->id=='object') && (in_array(Yii::app()->controller->action->id,array('update','view','admin','index'))) ? true : false)
					),
          array(
                'label'=>Yii::t('cms','历史记录'), 
                'url'=>array('object/history'),
                'active'=>Yii::app()->controller->id=='object' && Yii::app()->controller->action->id=='history'
                ),
          array(
                'label'=>Yii::t('cms','管理文章模板'), 
                'url'=>array('templete/admin'),
                'active'=> ((Yii::app()->controller->id=='templete') && (in_array(Yii::app()->controller->action->id,array('update','view','admin','index')))) ? true : false
          ),
				)
			),

		//Category Menu 
		array(
			'label'=>'<span id="menu_taxonomy" class="micon"></span>'.Yii::t('cms','分类'), 
			'url'=>'javascript:void(0);','linkOptions'=>array('id'=>'menu_2','class'=>'menu_2'),  
			'itemOptions'=>array('id'=>'menu_2'),
			'items'=>array(
				array(
					'label'=>Yii::t('cms','管理分类'), 
					'url'=>array('oterm/index'),
					'active'=> ((Yii::app()->controller->id=='oterm') && (in_array(Yii::app()->controller->action->id,array('index','root','create','show','update','edit')))) ? true : false)                                                                                     
				),

			),

      //Page Menu 
      	array(
      		'label'=>'<span id="menu_page" class="micon"></span>'.Yii::t('cms','页'), 
      		'url'=>'javascript:void(0);',
      		'linkOptions'=>array('id'=>'menu_3','class'=>'menu_3'), 
      		'itemOptions'=>array('id'=>'menu_3'),
      		'items'=>array(
      			array(
      				'label'=>Yii::t('cms','创建页面'), 
      				'url'=>array('page/create'),
      				'active'=>Yii::app()->controller->id=='page' && Yii::app()->controller->action->id=='create'
      			),
      			array(
      				'label'=>Yii::t('cms','管理页面'), 
      				'url'=>array('page/admin'),
      				'active'=> ((Yii::app()->controller->id=='page') && (in_array(Yii::app()->controller->action->id,array('update','view','admin','index')))) ? true : false
      			)
      		)
      ),

      //Resource Menu 
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

      //Resource Menu 
      array(
          'label'=>'<span id="menu_block" class="micon"></span>'.Yii::t('cms','自定义模块'), 
          'url'=>'javascript:void(0);',
          'linkOptions'=>array('id'=>'menu_5','class'=>'menu_5'), 
          'itemOptions'=>array('id'=>'menu_5'), 
          'items'=>array(
            array(
              'label'=>Yii::t('cms','创建模块'), 
              'url'=>array('block/create'),
              'active'=>Yii::app()->controller->id=='block' && Yii::app()->controller->action->id=='create'              
            ),
            array(
              'label'=>Yii::t('cms','管理模块'), 
              'url'=>array('block/admin'),
              'active'=> ((Yii::app()->controller->id=='block') && (in_array(Yii::app()->controller->action->id,array('update','view','admin','index')))) ? true : false
            )
          )
      ),
    ),
));
?>
</div>
<script type="text/javascript">

      $(document).ready(function () {
            //Hide the second level menu
            $('#left-sidebar ul li ul').hide();            
            //Show the second level menu if an item inside it active
            $('li.list_active').parent("ul").show();
            
            $('#left-sidebar').children('ul').children('li').children('a').click(function () {                    
                
                 if($(this).parent().children('ul').length>0){                  
                    $(this).parent().children('ul').toggle("200");    
                 }
                 
            });
          
            
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
