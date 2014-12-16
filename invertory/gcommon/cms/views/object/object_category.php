<div class="content-box">
    <div class="content-box-header">
    <h3><?php echo  Yii::t('cms',$root->name); ?></h3>
    </div> 
    <div class="content-box-content" style="display: block;">
            <div class="tab-content default-tab" style="display: block;">
                 <?php foreach($terms as $key=>$term) : ?>
                    <?php if(in_array($term['id'], $select_terms)): ?>
                        <?php $checked = true; ?>
                    <?php endif;?>
                    
                    <input type="checkbox" name="Oterm[]" <?php if(in_array($term['id'], $select_terms)): ?> <?php echo 'checked=checked'; ?> <?php endif;?>  value=<?php echo $term['id']; ?> /> <?php echo str_repeat("-",$term['level'] ).$term['name']?><br />  
                 <?php endforeach; ?>         
            </div>       
    </div>
</div>