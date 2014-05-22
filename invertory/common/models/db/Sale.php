<?php
namespace common\models\db;
use Yii;
use yii\db\Query;
use gcommon\cms\models\Db;
class Sale extends Db{
    protected $_dbType = "sale";
    public function getDbType(){
        return $this->_dbType;
    }
    /*sale_id,sale_userid,sale_subject,sale_catid,sale_price,sale_province,sale_city,sale_gender,sale_cdate
    form dog_saleinfo where sale_itemtype=1 and sale_haspic=1 order by sale_cdate desc limit 8*/
    /**
     * [getData description]
     * @return [type] [description]
     */
    public function getData($params = null){
        $query = new Query;
        $result = $query
                ->select('sale_id,sale_userid,sale_subject,sale_catid,sale_price,sale_province,sale_city,sale_gender,sale_cdate')
                ->from('dog_saleinfo')
                ->where('sale_itemtype=1 and sale_haspic=1 and sale_status=2')
                ->orderBy('sale_bid_click desc')
                ->limit(8)
                ->all(self::getDb());
        if($result){
            $rs = [];
            foreach($result as $key=>$value){
                $ret['sale_id'] = $value['sale_id'];
                $ret['sale_price'] = $value['sale_price'];
                $ret['sale_userid'] = $value['sale_subject'];
                $ret['sale_catid'] = $value['sale_catid'];
                $ret['sale_province'] = $value['sale_province'];
                $ret['sale_city'] = $value['sale_city'];
                $ret['sale_gender'] = $value['sale_gender'];
                $ret['sale_image'] = $this->get_file_url($value['sale_id'],'attachments/sale').'/'.$value['sale_id'].'.jpg';
                $ret['spe_name'] = $this->getSpecies($value['sale_catid']);
                $rs[] = $ret;
            }
        }
        return $rs;
    }

    /**
     * [get_file_url description]
     * @return [type] [description]
     */
    public function get_file_url($fileid,$root) {
        $UPLOAD_SERVER_CACHE2 = 'http://up1.goumin.com/';
        $sub[0] = $fileid;
        $sub[1] = $sub[0]>>8;
        $sub[2] = $sub[1]>>8;
        $sub[3] = $sub[2]>>8;
        $sub[4] = $sub[3]>>8;
        $dir = $root.'/'.$sub[4].'/'.$sub[3].'/'.$sub[2].'/'.$sub[1];
        return $UPLOAD_SERVER_CACHE2.$dir;
    }

    /*select spe_name_s from dog_species where spe_id=$speid
    获取犬种*/

    /**
     * [getSpecies description]
     * @return [type] [description]
     */
    public function getSpecies($speid){
        if($speid){
            $query = new Query;
            $result = $query
                    ->select('spe_name_s')
                    ->from('dog_species')
                    ->where("spe_id=$speid")
                    ->one(self::getDb());
            $result['spe_name_s'] = explode("/",$result['spe_name_s']);
            return $result['spe_name_s'][0];
        }
        return false;
    }

    /**
     * [getDb description]
     * @return [type] [description]
     */
    public static function getDb(){
        return \Yii::$app->get("dogdb");
    }

}