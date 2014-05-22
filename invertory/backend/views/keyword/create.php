<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var common\models\User $model
 */
$sidebar_name = '管理敏感词';
$sidebars = [
        [
            'name' => '敏感词',
            'icon' => 'tasks',
            'url' => '/manager/admin',
        ],
        [
            'name' => '添加敏感词',
            'icon' => 'tasks',
            'url' => '/manager/create',
        ],
    ];
$this->title = '敏感词';
?>
<?php include(__DIR__."/../layouts/base_sidebar.php");?>
<div class="content">
<div class="site-signup">
	<h1>添加敏感词</h1>
	<div class="row">
		<div class="col-lg-5">
			<?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>
				敏感词:<input type="text" name="keyword">
				<div class="form-group">
					<?= Html::submitButton('提交', ['class' => 'btn btn-primary']) ?>
				</div>
			<?php ActiveForm::end(); ?>
		</div>
	</div>
</div>
</div>