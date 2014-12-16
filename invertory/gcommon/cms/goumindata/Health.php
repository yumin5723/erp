<?php
namespace gcommon\cms\goumindata;
use Yii;
use yii\db\Query;
use gcommon\cms\components\GData;

class Health extends GData{
    public static function tableName(){
        return "dog_health_case";
    }
    //获取医生原创
    public function getHealthCaseList(){
        $query = new Query;
        $results = $query
            ->select('t1.id, t1.doctorid,t1.name,t2.73_img')
            ->from(['dog_health_case t1','dog_health t2'])
            ->where('t1.doctorid=t2.user_id')
            ->orderBy('t1.addtime desc')
            ->limit(10)
            ->all(self::getDb());
        if($results){
            $rs =[];
            foreach($results as $key=>$value){
                $ret['name'] = $value['name'];
                $ret['img'] = "http://s.goumin.com/{$value['73_img']}";
                $rs[] = $ret;
            }
        }
        return $results;
    }
    //SELECT qst_cat2 FROM dog_ask_question left join pre_common_member n on qst_userid=n.uid WHERE qst_cat1=1 and qst_cat2>99 GROUP BY qst_cat2;
    public function getDepartmentInfo(){
        $doctorIdsStr = $this->selectAllDoctorUid();
        $query = new Query;
        $result = $query
                ->select('t1.ans_content,t2.qst_subject,t2.qst_id')
                ->from(['dog_ask_answer t1','dog_ask_question t2'])
                ->where("t1.ans_qstid=t2.qst_id and t1.ans_userid in ({$doctorIdsStr})")
                ->orderBy('t1.ans_cdate desc')
                ->limit('3')
                ->all(self::getDb());



        echo "<pre>";
            print_r($result);
        echo "</pre>";
        exit;

        $query = new Query;
        $result = $query
                ->select('qst_cat2')
                ->from('dog_ask_question')
                ->leftJoin('pre_common_member p','qst_userid=p.uid')
                ->where('qst_cat1=1 and qst_cat2>99')
                ->groupBy('qst_cat2')
                ->all(self::getDb());

    }

    //获取医生UID集合
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

}