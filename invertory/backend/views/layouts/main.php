<?php
use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;

/**
 * @var \yii\web\View $this
 * @var string $content
 */

$menu = require(__DIR__.'/../../config/menu.php');
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <style type="text/css">
    .navbar-inverse{ background-color: #DDDDE2; border-color:#9898A7;}
    .navbar-inverse .navbar-brand{ color:#646475;}
    #main-menu {
      background: #f3f3f5;
      border-bottom: 0px;
      position: relative;
      border-top: 1px solid #fff;
      border-bottom: 1px solid #bcbcc6;
      z-index: 1;
    }
    </style>
</head>
<body>
	<?php $this->beginBody() ?>
	
	<div class="wrap">
		<nav role="navigation" id="w0">
			<div class="navbar-inverse navbar-fixed-top navbar" >
				<div class="navbar-header">
					<a href="/" class="navbar-brand">后台管理</a>
				</div>
				<div class="collapse navbar-collapse navbar-w0-collapse">
					<ul class="navbar-nav navbar-right nav" id="w1">
						<li><a href="/site/logout">Logout <?php if(Yii::$app->user->id):?>(<?=Yii::$app->user->identity->username?>)<?php endif;?></a></li>
					</ul>
				</div>
			<?php if(Yii::$app->user->id):?><div id="main-menu">
			  <ul class="nav nav-tabs">
			  	<?php foreach($menu['Administrator'] as $value):?>
			        <li class=" "><a href="<?=$value['url']?>"><i class="icon-"></i> <span><?=$value['name']?></span></a></li>
			    <?php endforeach;?>
			      </ul>
			</div><?php endif;?>
			</div>
		</nav>
	</div>
	<?= Breadcrumbs::widget([
		'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
	]) ?>
	<?= $content ?>
	<footer class="footer">
		<div class="">
		<p class="pull-left">&copy; My Company <?= date('Y') ?></p>
		<p class="pull-right"><?= Yii::powered() ?></p>
		</div>
	</footer>

    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
<script type="text/javascript">
$(".delete").attr("onClick",'return confirm("是否确认将此条信息删除?")');
</script>