<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
/**
* @var yii\web\View $this
* @var yii\gii\generators\crud\Generator $generator
*/
?>
<div id="sidebar-nav">
          <ul class="nav nav-list" id="dashboard-menu">
              <?php foreach ($sidebars as $key => $value):?>
              <li class=" theme-mobile-hack <?php if($sidebar_name == $value['name']):?> cur <?php endif;?>" ><a href="<?=$value['url']?>"><i class="icon-tasks"></i> <span><?=$value['name']?></span></a></li>
            <?php endforeach;?>
           </ul>
    </div>