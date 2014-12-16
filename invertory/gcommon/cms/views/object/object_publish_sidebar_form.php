<div id="inner-form-sidebar">
                    <!--Start the publish Box -->
                    <div class="content-box">
                     <div class="content-box-header">
                            <h3 style="cursor: s-resize;">模板</h3>
                    </div>
                    <div style="display: block;" class="content-box-content">

                    <div style="display: block;" class="tab-content default-tab">

                        <div class="row odd buttons border-left-silver">                                                                                       
                            <select id="Object_object_status" name="Templete[id]" class="status_select">
                                <?php foreach($templetes as $t) {?>
                                    <?php if($isNew == false && !empty($templete) && $t['id'] == $templete->templete_id){?>
                                        <option  value="<?php echo $t['id']?>" selected="selected"><?php echo $t['name']?></option>
                                    <?php } else {?>
                                        <option  value="<?php echo $t['id']?>" ><?php echo $t['name']?></option>
                                    <?php }?>
                                <?php }?>
                            </select>                                            
                        </div>                                    
                    </div>       
                    </div>
                </div>

                <!-- <div class="content-box">
                     <div class="content-box-header">
                            <h3 style="cursor: s-resize;">是否热门聚焦</h3>
                    </div>
                    <div style="display: block;" class="content-box-content">
                    <div style="display: block;" class="tab-content default-tab">
                        <div class="row odd buttons border-left-silver">                                       
                            <?php echo $form->radioButtonList($model,'ishot',array('1'=>'是','0'=>'否')); ?>
                        </div>                                    
                    </div>       
                    </div>
                </div>

                <div class="content-box">
                     <div class="content-box-header">
                            <h3 style="cursor: s-resize;">是否推荐资讯头条</h3>
                    </div>
                    <div style="display: block;" class="content-box-content">
                    <div style="display: block;" class="tab-content default-tab">
                        <div class="row odd buttons border-left-silver">                                       
                            <?php echo $form->radioButtonList($model,'istop',array('1'=>'是','0'=>'否')); ?>
                        </div>                                    
                    </div>       
                    </div>
                </div>

                <div class="content-box">
                     <div class="content-box-header">
                            <h3 style="cursor: s-resize;">是否图文推荐</h3>
                    </div>
                    <div style="display: block;" class="content-box-content">
                    <div style="display: block;" class="tab-content default-tab">
                        <div class="row odd buttons border-left-silver">                                       
                            <?php echo $form->radioButtonList($model,'isred',array('1'=>'是','0'=>'否')); ?>
                        </div>                                    
                    </div>       
                    </div>
                </div> -->




                    <div class="content-box">

                            <div class="content-box-header">


                            <h3><?php echo  Yii::t('cms','提交'); ?></h3>

                            </div> 

                            <div class="content-box-content" style="display: block;">

                                    <div class="tab-content default-tab" style="display: block;">

                                        <?php echo $form->label($model,'发布时间'); ?>
                                        <?php echo $form->textField($model,'object_date'); ?>
                                        <?php echo $form->error($model,'object_date'); ?>

                                           
                                        <?php $this->renderPartial('/object/object_workflow',array('form'=>$form,'model'=>$model,'content_status'=>$content_status,'type'=>$type)); ?>
                                    </div>       

                            </div>


                    </div>
                    <?php foreach($roots as $key=>$root) : ?>  
                    <?php $terms = Oterm::model()->getAllDescendantsByRoot($root->id); ?>
                    <?php $ret = array(); ?>
                    <?php if ($isNew == false) :?>
                        <?php $select_terms = ObjectTerm::model()->findAllByAttributes(array("object_id"=>$model->object_id)); ?>
                        <?php 
                            $ret = array();
                            foreach($select_terms as $select){
                                $ret[] = $select->term_id;
                            }
                        ?>
                    <?php endif; ?>
                    <?php $this->renderPartial('/object/object_category',array(
                            'root'=>$root,
                            'terms'=>$terms,
                            'select_terms'=>$ret,
                        )); ?>
                    <?php endforeach; ?>
                    
                
                    
            </div>
