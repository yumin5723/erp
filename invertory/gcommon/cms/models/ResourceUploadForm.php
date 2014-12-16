<?php

class ResourceUploadForm extends CFormModel
{
    public $name;
    public $body;
    public $link;
    public $upload;
    public $type;
    public $where;
    
    public function rules()
    {
        return array(
            array('link, name, body, type,where','safe'),
            array('upload', 'file','allowEmpty'=>true,'types'=>'jpg,jpeg,png,gif,swf,flv'),
            
        );
    }
    
    
    public function attributeLabels()
    {
            return array(
                    'upload'=>Yii::t('cms','上传'),
                    'link'=>Yii::t('cms','链接'),
                    'name'=>Yii::t('cms','资源名称'),
                    'body'=>Yii::t('cms','描述'),
                    'where'=>Yii::t('cms','Storage'),
                    'type'=>Yii::t('cms','文件类型')
            );
    }
    
    
}