<?php

/**
 * Class for handle Upload to Local Storage
 * 
 * 
 * @version 1.0
 * @package common.storages
 */

 //This is a must Have Resource Class - Don't Delete this
class LocalStorage
{
      
      public $resource_url;
      public $base_folder;
      public $resource_folder;
     
      public $max_file_size="30720000";
      public $min_file_size="5";
      public $allow_types=array();
      
      public function __construct($allow_types=array()) {
            $this->max_file_size=$this->max_file_size;
            $this->min_file_size=$this->min_file_size;
            $this->allow_types=$allow_types;
            $this->resource_url = Yii::app()->params['resource_url'];
            $this->base_folder = Yii::app()->params['resource_folder'];
      }
        
      public function UploadFile(&$resource,$model,&$process,&$message,$remote=false){
            $this->resource_folder = $this->base_folder.$model->type;
            if($model->upload->size > $this->max_file_size){
                $allow_size=$this->max_file_size/(1024*1024);                      
                $model->addError('upload', Yii::t('cms','File size is larger than allowed size : ').$allow_size. ' mb');
                $process=false;
                return false;
            }  
            
            if($model->upload->size < $this->min_file_size){
                $model->addError('upload', t('cms','File is too small!'));
                $process=false;
                return false;
            } 
            
            if(count($this->allow_types)>0){
                if(!in_array(strtolower(CFileHelper::getExtension($model->upload->name)), $this->allow_types)){
                    $model->addError('upload', t('cms','File extension is not allowed!'));
                    $process=false;
                    return false;
                }
            }
            $filename=$resource->resource_name=$model->upload->name;            

            $md5 = md5_file($model->upload->tempName);
            $p = strrpos($filename, ".");
            $filename = substr($filename,0,$p)."--".substr($md5,0,5)."--".substr($filename, $p);

            // $filename=$this->gen_uuid();   
            // $filename=str_replace(" ","-",$filename) ;
            
            // folder for uploaded files
            $folder=date('Y').DIRECTORY_SEPARATOR.date('m').DIRECTORY_SEPARATOR; 
            if (!(file_exists($this->resource_folder.DIRECTORY_SEPARATOR.$folder) && 
                (is_dir($this->resource_folder.DIRECTORY_SEPARATOR.$folder)))){
                mkdir($this->resource_folder.DIRECTORY_SEPARATOR.$folder,0777,true);
            }
                     
            // $filename=$filename.'.'.strtolower(CFileHelper::getExtension($model->upload->name));
            $path=$folder.$filename;
            if($model->upload->saveAs($this->resource_folder.DIRECTORY_SEPARATOR.$path)){               
                $resource->resource_path=$model->type.DIRECTORY_SEPARATOR.$path;             
                //Resource::generateThumb($model->upload->name,$folder,$filename);                              
                $process=true;
                return true;
            } else {
                $process=false;
                $message=t('cms','Error while Uploading. Try again later.');
                return false;
              
            }
      }

      public function getRemoteFile(&$resource,$model,&$process,&$message,$path,$ext,$changeresname=true){
            
            if(count($this->allow_types)>0){
                if(!in_array(strtolower($ext), $this->allow_types)){
                    $message=t('cms','File extension is not allowed!');
                    $process=false;
                    return false;
                }
            }
                                            
            $ch = curl_init($path);
            curl_setopt($ch,CURLOPT_HEADER,0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
            $rawdata=curl_exec($ch);
            curl_close($ch);
            
            if(!$rawdata) {
                $process=false;
                $message=t('cms','Error while getting Remote File. Try again later.');
                return false;
            }
            
                        
            $filename=$this->gen_uuid();
            
            // folder for uploaded files
            $folder=date('Y').DIRECTORY_SEPARATOR.date('m').DIRECTORY_SEPARATOR; 
            if (!(file_exists($this->resource_folder.DIRECTORY_SEPARATOR.$folder) && 
                (is_dir($this->resource_folder.DIRECTORY_SEPARATOR.$folder)))){
                mkdir($this->resource_folder.DIRECTORY_SEPARATOR.$folder,0777,true);
            }
            
            
            //Check if File exists, so Rename the Filename again;
             while (file_exists($this->resource_folder.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR.$filename.'.'
             .strtolower($ext))) {
                 $filename .= rand(10, 99);
             }
            
            
            $filename=$filename.'.'.$ext;
            
            $path=$folder.$filename;
            $fullpath=$this->resource_folder.DIRECTORY_SEPARATOR.$path;         
            
            $fp = fopen($fullpath,'x');
            fwrite($fp, $rawdata);
            fclose($fp);
                        
            $resource->where=$model->where;                 
            $resource->resource_path=$path;
            
            if($changeresname){
                $resource->resource_name=$filename;
            }
            
            //Resource::generateThumb($filename,$folder,$filename);
            $process=true;
            return true;
      }
      
      public function getFilePath($file){
            return $this->resource_url.'/'.$file;
      }
      
      public function deleteResource($resource){
                    
            if(file_exists($this->resource_folder.DIRECTORY_SEPARATOR.$resource->resource_path)){
                unlink($this->resource_folder.DIRECTORY_SEPARATOR.$resource->resource_path);
            }
            return;
      }
      /**
     * Generate Unique File Name for the File Upload
     * 
     */
     function gen_uuid($len=8) {

        $hex = md5('upload' . uniqid("", true));

        $pack = pack('H*', $hex);
        $tmp =  base64_encode($pack);

        $uid = preg_replace("/[^A-Za-z0-9]/", "", $tmp);

        $len = max(4, min(128, $len));

        while (strlen($uid) < $len)
            $uid .= gen_uuid(22);

        $res=substr($uid, 0, $len);
        return $res;
    }
        
}
