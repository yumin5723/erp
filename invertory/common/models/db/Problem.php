<?php
namespace common\models\db;
use Yii;
use yii\db\Query;
use gcommon\cms\models\Db;
class Problem extends Db{
    protected $_dbType = "problem";
    public function getDbType(){
        return $this->_dbType;
    }
    public function getData($params = null){
        $arr = $this->getProblemInfo();
        $res = [];
        foreach($arr as $key=>$value){
            foreach($value as $k=>$val){
                $res[$val["qst_cat2"]][$val["cat_name"]][$k]['d_img'] = static::getDocImgById($val['ans_userid']);
                $res[$val["qst_cat2"]][$val["cat_name"]][$k]['qst_subject'] = $val["qst_subject"];
                $res[$val["qst_cat2"]][$val["cat_name"]][$k]['ans_content'] = strip_tags($val["ans_content"]);
                $res[$val["qst_cat2"]][$val["cat_name"]][$k]['qst_id'] = strip_tags($val["qst_id"]);
                $res[$val["qst_cat2"]][$val["cat_name"]][$k]['d_userid'] = $val['ans_userid'];
            }
        }
        return $res;
    }

    /*select t1.ans_content,t2.qst_subject,t2.qst_cat2,t3.cat_name from dog_ask_answer t1','dog_ask_question t2','dog_ask_catalog t3
    t1.ans_qstid=t2.qst_id and t2.qst_cat2=t3.cat_id and t1.ans_userid in ({$doctorIdsStr}) and t2.qst_cat2={$val['cat_id']}
    order by t1.ans_cdate desc limit 3
    获取数据*/
    /**
     * [getProblemInfo description]
     * @return [type] [description]
     */
    public function getProblemInfo(){
        $doctorIdsStr = $this->selectAllDoctorUid();
        $depid = $this->getDepartmentId();
        if($depid){
            $ret = [];
            foreach($depid as $key=>$val){
                $query = new Query;
                $docarr = $query
                    ->select('t1.ans_content,t2.qst_id,t2.qst_subject,t2.qst_id,t2.qst_cat2,t3.cat_name,t1.ans_userid')
                    ->from(['dog_ask_answer t1','dog_ask_question t2','dog_ask_catalog t3'])
                    ->where("t2.status=1 and t1.ans_qstid=t2.qst_id and t2.qst_cat2=t3.cat_id and t1.ans_userid in ({$doctorIdsStr}) and t2.qst_cat2={$val['cat_id']}")
                    ->orderBy('t1.ans_cdate desc')
                    ->limit('3')
                    ->all(self::getDb());
                $ret[] = $docarr;
            }
            return $ret;
        }
    }

    /*select cat_id from dog_ask_catalog where cat_id>99
    获取科室ID*/
    /**
     * [getDepartmentId description]
     * @return [type] [description]
     */
    public function getDepartmentId(){
        $query = new Query;
        $result = $query
            ->select('cat_id')
            ->from('dog_ask_catalog')
            ->where('cat_id>99')
            ->all(self::getDb());
        if($result){
            return $result;
        }
        return false;

    }
    /*select user_id dog_health where 1
    获取医生UID集合
    */
    /**
     * [selectAllDoctorUid description]
     * @return [type] [description]
     */
    public function selectAllDoctorUid(){
        $query = new Query;
        $result = $query
            ->select('user_id')
            ->from('dog_health')
            ->where('1')
            ->all(self::getDb());
        if($result){
            $doctorIds_arr = [];
            foreach($result as $key=>$val){
                $doctorIds_arr[] = $val['user_id'];
            }
            $doctorIdsStr = implode(',', $doctorIds_arr);
            return $doctorIdsStr;
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
    public static function getDocImgById($d_id){
        $query = new Query;
        $result = $query
            ->select('73_img as img')
            ->from('dog_health')
            ->where(["user_id"=>$d_id])
            ->one(self::getDb());
        if(empty($result)){
            return '';
        }
        return $result['img'];
    }

}