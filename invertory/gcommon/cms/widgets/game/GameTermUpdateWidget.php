<?php
/**
 * This is the Widget for Updating a Term
 * 
 * @author Tuan Nguyen <nganhtuan63@gmail.com>
 * @version 1.0
 * @package  cmswidgets.object
 *
 */
class GameTermUpdateWidget extends CWidget
{
    
    public $visible=true;     
 
    public function init()
    {
        
    }
 
    public function run()
    {
        if($this->visible)
        {
            $this->renderContent();
        }
    }
 
    protected function renderContent()
    {       
            $term_id=isset($_GET['id']) ? (int)$_GET['id'] : 0;     
            $model=GxcHelpers::loadDetailModel('GameTerm', $term_id);                
            // if it is ajax validation request
            if(isset($_POST['ajax']) && $_POST['ajax']==='term-form')
            {
                    echo CActiveForm::validate($model);
                    Yii::app()->end();
            }
            // collect user input data
            if(isset($_POST['GameTerm']))
            {
                    $model->attributes=$_POST['GameTerm'];                        
                    if($model->save()){                            
                        Yii::app()->user->setFlash('success',Yii::t('cms','Update Term Successfully!'));                                                                
                    }
            }
            Yii::app()->controller->layout=isset($_GET['embed']) ? 'clean' : 'main';
            $this->render('cmswidgets.views.gameterm.game_term_form_widget',array('model'=>$model));                                
    }   
    
    
}