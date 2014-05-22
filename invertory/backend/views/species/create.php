<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var common\models\User $model
 */
$this->title = '创建关键词';
$sidebar_name = '创建关键词';
$sidebars = [
        [
            'name' => '管理关键词',
            'icon' => 'tasks',
            'url' => '/species/admin',
        ],
        [
            'name' => '创建关键词',
            'icon' => 'tasks',
            'url' => '/species/create',
        ],
    ];
?>
<?php include(__DIR__."/../layouts/base_sidebar.php");?>
<div class="content">
<div class="site-signup">

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>
                <?= $form->field($model, 'spe_id') ?>
                <?= $form->field($model, 'spe_title') ?>
                <div class="form-group">
                    <?= Html::submitButton('提交', ['class' => 'btn btn-primary']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
</div>