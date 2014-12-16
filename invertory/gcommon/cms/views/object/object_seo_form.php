                          <div class="content-box-header">


                            <h3><?php echo  Yii::t('cms','摘要 & SEO');?></h3>
                             <ul class="content-box-tabs">
                                <li><a class="default-tab current" href="#summary_box"><?php echo  Yii::t('cms','摘要');?></a></li>
                                <li><a href="#seo_box" class=""><?php echo  Yii::t('cms','SEO');?></a></li>
                             </ul>
                            </div> 

                            <div class="content-box-content" style="display: block;">

                                    <div class="tab-content default-tab" id="summary_box">
                                        <?php echo $form->label($model,'作者'); ?>
                                        <?php echo $form->textField($model,'object_author_name',array('id'=>'txt_object_author_name')); ?>
                                        <?php echo $form->error($model,'object_author_name'); ?>
                                        
                                        <?php echo $form->labelEx($model,'标签'); ?>
                                        <?php $this->widget('CAutoComplete', array(
                                                'model'=>$model,
                                                'attribute'=>'tags',
                                                'url'=>array('suggestTags'),
                                                'multiple'=>true,
                                                'htmlOptions'=>array('size'=>50,'id'=>'txt_object_tags'),
                                        )); ?>

                                        <?php echo $form->error($model,'tags'); ?>

                                        <?php echo $form->label($model,'文章副标题'); ?>
                                        <?php echo $form->textArea($model,'object_excerpt',array('tabindex'=>'3','id'=>'txt_object_excerpt')); ?>
                                        <?php echo $form->error($model,'object_excerpt'); ?>
                                        
<!--                                         <?php echo $form->labelEx($model,'comment_status'); ?>
                                        <?php echo $form->dropDownList($model,'comment_status',  ConstantDefine::getObjectCommentStatus()); ?>
                                        <?php echo $form->error($model,'comment_status'); ?> -->
                                        
                                        
                                    </div>    
                                    <div class="tab-content" id="seo_box">
                                        
                                        <?php echo $form->label($model,'文章摘要'); ?>
                                        <?php echo $form->textField($model,'object_slug',array('id'=>'txt_object_slug')); ?>
                                        <?php echo $form->error($model,'object_slug'); ?>
                                        
                                        <?php echo $form->label($model,'文章标题'); ?>
                                        <?php echo $form->textField($model,'object_title',array('id'=>'txt_object_title')); ?>
                                        <?php echo $form->error($model,'object_title'); ?>
                                        
                                        <?php echo $form->label($model,'文章关键字'); ?>
                                        <?php echo $form->textArea($model,'object_keywords',array('id'=>'txt_object_keywords')); ?>
                                        <?php echo $form->error($model,'object_keywords'); ?>
                                        
                                        <?php echo $form->label($model,'文章描述'); ?>
                                        <?php echo $form->textArea($model,'object_description',array('id'=>'txt_object_description')); ?>
                                        <?php echo $form->error($model,'object_description'); ?>
                                        
                                    </div>       

                            </div>

