 <div id="titlewrap">
         <?php echo $form->textField($model,'object_name',array('class'=>'specialTitle','tabindex'=>'1','id'=>'txt_object_name')); ?>
         <?php echo $form->error($model,'object_name'); ?>                                  
</div>
 <div id="titlewrap">
  列表标题
         <?php echo $form->textField($model,'object_list_name',array('class'=>'specialTitle','tabindex'=>'2','id'=>'txt_object_list_name')); ?>
         <?php echo $form->error($model,'object_list_name'); ?>                                  
</div>
<div id="small_buttons_insert" align="right">
        <span><?php echo  Yii::t('cms','插入'); ?></span>      
        <img valign="top" alt="Image" title="Image" onClick="insertFileToContent('image');" src="<?php echo $this->module->assetsUrl; ?>/images/insert_image.png" />
                
</div>
<div id="bodywrap">     
         <?php echo $form->textArea($model,'object_content',array('tabindex'=>'2','class'=>'specialContent','id'=>'ckeditor_content')); ?>
         <?php echo $form->error($model,'object_content'); ?>                                                          
</div>
<?php if($model->isNewRecord) : ?>
<script type="text/javascript">
 $(document).ready(function() {
    // We will hide all the term that not belongs to current language
   changeTermsLanguage();   
   $("#lang_select").change(function() {
      changeTermsLanguage();
   });
 });

 function changeTermsLanguage(){
     var current_language=$("#lang_select").val();                                                        
     $('.taxonomy_lang_wrap').each(function() {                                     

         var taxonomy=$(this).attr('id');         
         taxonomy=taxonomy.split('_');                
         //lang=taxonomy[2] and id=taxonomy[1]
         if(taxonomy[2].toString()!=current_language.toString()){
             $(this).hide();             
             //We will re-init input checkbox of these terms
             $("#selected_terms_"+taxonomy[1]).empty();
             $("#list_terms_"+taxonomy[1]).children('span').remove();             
             $.each(window['array_term_'+taxonomy[1]], function(k,v) {   
                   if(!$("#selected_terms_"+taxonomy[1]).children().children('#'+k).length>0){
                        $('#list_terms_'+taxonomy[1]).append('<span rel="'+v.name+'"><input value="'+v.id+'_'+taxonomy[1]+'" id="'+k+'" onChange="checkATerm'+taxonomy[1]+'(\''+k+'\',this);" type="checkbox" name="terms[]" /> '+v.name+'<br/></span>');
                   }
             });      

         } else {
             $(this).show();
         }                  
      });
 }
</script>
<?php endif; ?>