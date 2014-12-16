<?php 
    $this->pageTitle=Yii::t('cms','模板列表'); 
?>
<div id="site-content">
            <?php $this->renderPartial('/layouts/menu'); ?>
        <div id="main-content-zone">
                        <?php if(isset($this->menu)) :?>
                        <?php if(count($this->menu) >0 ): ?>
            <div class="header-info">
                <?php                                       
                      $this->widget('zii.widgets.CMenu', array(
                              'items'=>$this->menu,
                              'htmlOptions'=>array(),
                      ));                                       
                ?>
            </div>
                        <?php endif; ?>
                        <?php endif; ?>
                        <?php $this->widget( 'zii.widgets.grid.CGridView', array(
		'id'=>'taxonomy-grid',
		'dataProvider'=>$model->search(),
		'filter'=>$model,
		'summaryText'=>Yii::t( 'cms', 'Displaying' ).' {start} - {end} '.Yii::t( 'cms', 'in' ). ' {count} '.Yii::t( 'cms', 'results' ),
		'pager' => array(
			'header'=>Yii::t( 'cms', 'Go to page:' ),
			'nextPageLabel' =>  Yii::t( 'cms', 'Next' ),
			'prevPageLabel' =>  Yii::t( 'cms', 'previous' ),
			'firstPageLabel' =>  Yii::t( 'cms', 'First' ),
			'lastPageLabel' =>  Yii::t( 'cms', 'Last' ),
			// 'pageSize'=> Yii::app()->settings->get('system', 'page_size')
		),
		'columns'=>array(
			array( 'name'=>'id',
				'type'=>'raw',
				'htmlOptions'=>array( 'class'=>'gridmaxwidth' ),
				'value'=>'$data->id',
			),
			array(
				'name'=>'name',
				'type'=>'raw',
				'htmlOptions'=>array( 'class'=>'gridLeft' ),
				// 'value'=>'CHtml::link($data->name,array("'.app()->controller->id.'/view","id"=>$data->taxonomy_id))',
			),
			array(
				'name'=>'rar_file',
				'type'=>'raw',
				'htmlOptions'=>array( 'class'=>'gridLeft' ),
				'value'=>'$data->rar_file',
			),
			array(
				'name'=>'status',
				'type'=>'raw',
				'htmlOptions'=>array( 'class'=>'gridLeft' ),
				'value'=>'Templete::convertTempleteStatus($data->status)',
			),
			array(
				'class'=>'CButtonColumn',
				'template'=>'{update} {delete}',
				'buttons'=>array
				(
					'delete' => array
					(
						'label'=>Yii::t( 'cms', '删除' ),
						'imageUrl'=>false,
					),
					'update' => array
					(
						'label'=>Yii::t( 'cms', '修改' ),
						'imageUrl'=>false,
					),
				),
			),
		),
	) ); ?>

                    </div>
            </div>
        </div>
        
</div>
