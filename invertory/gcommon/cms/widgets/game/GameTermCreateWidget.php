<?php

/**
 * This is the Widget for Creating new Term
 * 
 * @version 1.0
 * @package  cmswidgets.object
 *
 *
 */
class GameTermCreateWidget extends CWidget
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
        $model = new GameTerm;                
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
                Yii::app()->user->setFlash('success',Yii::t('cms','Create new Term Successfully!'));   
                
                if(!isset($_GET['embed'])) {
                    $model=new GameTerm;
                    Yii::app()->controller->redirect(array('create'));  
                }
            }
        }
        
        Yii::app()->controller->layout=isset($_GET['embed']) ? 'clean' : 'main';
        $this->render('cmswidgets.views.gameterm.game_term_form_widget',array('model'=>$model));                        
        
    }   
}
