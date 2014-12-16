<?php if(Yii::$app->session->hasFlash('success')):?>
<div class="notification notesuccess png_bg alert-success">
<div>
 <?php echo Yii::$app->session->getFlash('success'); ?>
</div>
</div>
<script type="text/javascript" >
      $(".notification").delay(2100).fadeOut(1300);
</script>
<?php endif; ?>

<?php if(Yii::$app->session->hasFlash('error')):?>
<div class="notification noteerror png_bg alert alert-error">
<div>
 <?php echo Yii::$app->session->getFlash('error'); ?>
</div>
</div>
<script type="text/javascript" >
      $(".notification").delay(2100).fadeOut(1300);
</script>
<?php endif; ?>