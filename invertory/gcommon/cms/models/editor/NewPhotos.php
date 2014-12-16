<?php
namespace gcommon\cms\models\editor;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use gcommon\cms\components\CmsActiveRecord;
use gcommon\cms\components\ResizeImage;

class NewPhotos extends CmsActiveRecord{

	public static function tableName(){
		return "new_photo";
	}

	public function rules(){
		return [
			[['pht_id','pht_userid','pht_image','pht_cdate'],'required'],
		];
	}

	public function getAllOldData(){
		$pht_ids = $this->getAllIDs();
		if($pht_ids){
			$pht_ids = " and pht_id not in($pht_ids)";
		}
		$query = static::getDogDb()->createCommand("select max(pht_id) as max_pht_id from dog_photo");
		$maxs = $query->queryOne();
		$max_pht_id = $maxs['max_pht_id'];
		$command = static::getDogDb()->createCommand("select pht_id,pht_userid,pht_abmid,pht_title,pht_cdate,pht_fileext from dog_photo left join dog_album on pht_abmid=abm_id left join dogucenter.nd_user on pht_userid=user_id where pht_id>$max_pht_id-2000 and abm_species>=0 and pht_auth=0 and auth=0 and dog_photo.pht_cdate < unix_timestamp()-600 $pht_ids order by pht_id desc limit 1000");
		$rs = $command->queryAll();
		$provider = new ArrayDataProvider([
		    'allModels' => $rs,
		    'sort' => [
		        'attributes' => ['pht_id', 'pht_userid'],
		    ],
		    'pagination' => [
		        'pageSize' => 10,
		    ],
		]);
		return $provider;
	}


	public function getImage(){
		return function ($data){
			$pht_image = $this->get_file_url($data['pht_id']) . '/' . $data['pht_id'] . '.' . $data['pht_fileext'];
			return '<a href="http://www.goumin.com/album/'.$data['pht_abmid'].'/'.$data['pht_id'].'.html" target="_blank"><img src="'.$pht_image.'" width="50" height="50"/></a>';
		};
	}

	public function getAbmid($pht_id,$pht_userid){
		$command = static::getDogDb()->createCommand("select pht_abmid from dog_photo where pht_id=$pht_id and pht_userid=$pht_userid");
		$rs = $command->queryOne();
		$dog_album = "http://www.goumin.com/album/".$rs['pht_abmid'].'/'.$pht_id.'.html';
		return $dog_album;
	}

	public function get_file_url($fileid){
		// application root
		// $app_root = $_SERVER['DOCUMENT_ROOT'];
		// if($app_root=='')
		#$app_root = '/www/wwwroot/goumin.com/www';
		$app_root = dirname(dirname(__FILE__));
		$document_root = $app_root;
		$CUSTOM_FILE_URL = '/attachments';
		$CUSTOM_FILE_DIR = $document_root . '/attachments';
		$photo_url = $CUSTOM_FILE_URL . '/photo';
		$UPLOAD_SERVER_CACHE1 = 'http://up1.goumin.com';
		$root = $CUSTOM_FILE_URL . '/photo';
	    $sub[0] = $fileid;
	    $sub[1] = $sub[0] >> 8;
	    $sub[2] = $sub[1] >> 8;
	    $sub[3] = $sub[2] >> 8;
	    $sub[4] = $sub[3] >> 8;
	    $dir = $root . '/' . $sub[4] . '/' . $sub[3] . '/' . $sub[2] . '/' . $sub[1];
	    if ($root == $photo_url) {
	        if ($sub[2] < 40) {
	            return 'http://up1.goumin.com' . $dir;
	        } // old:img4
	        else {
	            if ($sub[2] < 60) {
	                return 'http://img4.goumin.com' . $dir;
	            }
	        }
	        // return $UPLOAD_SERVER.$dir;
	    } 
	    return $UPLOAD_SERVER_CACHE1 . $dir;
	}

	public static function getAhref(){
		return function ($data){
			return '<a href="/cms/editor/push-photo?pht_id='.$data['pht_id'].'">推荐到首页</a>';
		};
	}
	public function getAllNewData(){
		$data = static::find()->orderby("pht_id desc")->all();
		$rs = [];
		if($data){
			foreach ($data as $key => $v) {
				$rs[$key]['id'] = $v['id'];
				$rs[$key]['pht_id'] = $v['pht_id'];
				$rs[$key]['pht_image'] = $v['pht_image'];
				$rs[$key]['pht_userid'] = $v['pht_userid'];
				$rs[$key]['pht_cdate'] = date("Y-m-d H:i:s",$v['pht_cdate']);
			}
		}
		return $rs;
	}

	public function getAllIDs(){
		$data = static::find()->select("pht_id")->all();
		$ids = '';
		if($data){
			foreach ($data as $key => $v) {
				$ids .= $v['pht_id'].',';
			}
		}
		$ids = trim($ids,',');
		return $ids;
	}


	public function getDogDb(){
		return \Yii::$app->get("dogdb");
	}


	public function pushData($pht_id){
		$command = static::getDogDb()->createCommand("select pht_id,pht_userid,pht_abmid,pht_title,pht_fileext,pht_cdate from dog_photo where pht_id={$pht_id}");
		$data = $command->queryOne();
		if(!empty($data)){
			$pht_image = $this->get_file_url($data['pht_id']) . '/' . $data['pht_id'] . '.' . $data['pht_fileext'];
		}
		$model = new NewPhotos;
		if(!NewPhotos::findOne(['pht_id'=>$pht_id])){
			$data['pht_image'] = Yii::$app->params['targetDomain'].$this->getThumb($pht_image,$data['pht_id'],$data['pht_fileext']);
			if($model->load($data) && $model->save()){
				return true;
			}
		}
		return false;
	}


	public function cancelData($pht_id){
		$model = NewPhotos::findOne(['pht_id'=>$pht_id]);
		if($model){
			$model->delete();
		}
	}


	public function load($data, $formName = null)
	{
		$this->setAttributes($data);
        return true;
	}

	// public function getData($params = null){
	// 	$model = NewPhotos::find()->orderby("pht_cdate desc")->all();
	// 	$results = [];
	// 	if($model){
	// 		foreach ($model as $key => $v) {
	// 			$results[$key]['pht_id'] = $v['pht_id'];
	// 			$results[$key]['pht_image'] = $v['pht_image'];
	// 			$results[$key]['pht_userid'] = $v['pht_userid'];
	// 		}
	// 	}
	// 	return $results;
	// }

    /**
     * TODO 待整理 （李丁）
     * @param $headId 狗狗图片ID
     * @param $headExt 图片后缀名
     * @param $headDate 添加时间
     * @param string $size ''为大图 s 是中图 s2 是小图
     * @return string 狗狗头像URL
     */
    public function getDogHeadUrl($headId,$headExt,$headDate,$size = '')
    {
        $sub[0] = $headId;
        $sub[1] = $sub[0] >> 8;
        $sub[2] = $sub[1] >> 8;
        $sub[3] = $sub[2] >> 8;
        $sub[4] = $sub[3] >> 8;
        $dir = '/head' . '/' . $sub[4] . '/' . $sub[3] . '/' . $sub[2] . '/' . $sub[1];
        if ($headDate > 0 && $headDate < time() - 3600) {
            return 'http://hd2.goumin.com/attachments' . $dir.'/'.$headId.$size.'.'.$headExt;
        }
    }


    /**
     * TODO 待整理（李丁）
     * 获取团购图片
     * @param $fileId 商品ID
     * @param $date 修改时间
     * @return string 图片URL
     */
    public function getMall1Url($fileId,$date){
        $UPLOAD_SERVER_CACHE1 = 'http://up1.goumin.com';
        $UPLOAD_SERVER_CACHE2 = 'http://up2.goumin.com';
        $dir = '/attachments/mall';
        $sub[0] = $fileId;
        $sub[1] = $sub[0]>>8;
        $sub[2] = $sub[1]>>8;
        $sub[3] = $sub[2]>>8;
        $sub[4] = $sub[3]>>8;
        $url = $dir.'/'.$sub[4].'/'.$sub[3].'/'.$sub[2].'/'.$sub[1].'/' .$fileId . '.jpg?' .$date;
        if ( $fileId%3==0 ) {
            return $UPLOAD_SERVER_CACHE1.$url;
        }else{
            return $UPLOAD_SERVER_CACHE2.$url;
        }

    }


    /**
     * TODO 待整理（李丁）
     * 获取商城图片
     * @param $fileId 商品ID
     * @param $date 修改时间
     * @return string 图片URL
     */
    public function getMall2Url($fileId,$date){
        $UPLOAD_SERVER_CACHE1 = 'http://up1.goumin.com';
        $UPLOAD_SERVER_CACHE2 = 'http://up2.goumin.com';
        $dir = '/attachments/mall2';
        $sub[0] = $fileId;
        $sub[1] = $sub[0]>>8;
        $sub[2] = $sub[1]>>8;
        $sub[3] = $sub[2]>>8;
        $sub[4] = $sub[3]>>8;
        $url = $dir.'/'.$sub[4].'/'.$sub[3].'/'.$sub[2].'/'.$sub[1].'/' .$fileId . '.jpg?' .$date;
        if ( $fileId%3==0 ) {
            return $UPLOAD_SERVER_CACHE1.$url;
        }else{
            return $UPLOAD_SERVER_CACHE2.$url;
        }

    }

    /**
     * TODO 待整理（李丁）
     *  获取点评商品URL
     * @param $fileId 点评商品ID
     * @param $date 点评商品修改时间
     * @return string 点评商品URL
     */
    public function getPingGoodsUrl($fileId,$date)
    {
        $dir = '/attachments/goods';
        $sub[0] = $fileId;
        $sub[1] = $sub[0]>>8;
        $sub[2] = $sub[1]>>8;
        $sub[3] = $sub[2]>>8;
        $sub[4] = $sub[3]>>8;
        $url = $dir.'/'.$sub[4].'/'.$sub[3].'/'.$sub[2].'/'.$sub[1].'/' .$fileId . '.jpg?' .$date;
        return 'http://up1.goumin.com'.$url;
    }

    public function getThumb($url,$pht_id,$ext){
		$img = @file_get_contents($url,true);
		$attachDir = Yii::$app->params['attachDir'];
        $dir = '/album/'.'day_'.date('ymd').'/';
        $save_path = $attachDir . $dir;
        if(!is_dir($save_path)){
            @mkdir($save_path,0777,true);
        }
        $new_file_name = $pht_id.'s.'.$ext;
	    $file_path = $save_path.$new_file_name;
	    $realpath = $dir.$new_file_name;

	    @file_put_contents($file_path,$img);
		$resize = new ResizeImage;
		// $thumb = $resize->cut_img($file_path,262,246,$file_path);
        $thumb = $resize->getThumb($file_path,$realpath,$ext,262,246);
        unlink($file_path);
        return $thumb;
	}
}