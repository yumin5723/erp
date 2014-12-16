<?php
namespace gcommon\cms\components;
use Yii;
use yii\base\Behavior;
use yii\db\Query;
/**
 * Auto calulate dependence of cms models.
 * for page, template, content, block etc.
 */

include_once(dirname(dirname(__DIR__)) . '/lib/simple_html_dom.php');
class CmsDepBehavior extends Behavior {
    public $db;

    public $conn_id = "cmsdb";

    public $tableName = "obj_dependence";

    /**
     * update depentend block for current model
     * auto calculate from html string
     *
     * @param $html: string
     *
     * @return
     */
    public function UpdateDependentBlockByHtml($html) {
        $dom = str_get_html($html);
        $blocks = array();
        foreach ($dom->find("[data-block]") as $node) {
            $blocks[] = $node->attr['data-block'];
        }
        $blocks = array_unique($blocks);
        $model_type = $this->owner->getObjType();
        $model_id  = $this->owner->id;
        unset($dom);
        gc_collect_cycles();
        return $this->updateDependence($model_type,$model_id,'block',$blocks);
    }

    /**
     * function_description
     *
     * @param $category_ids:
     *
     * @return
     */
    public function UpdateDependentCategoryByIds(array $category_ids) {
        $model_type = $this->owner->getObjType();
        $model_id  = $this->owner->id;
        return $this->updateDependence($model_type,$model_id,'category',$category_ids);
    }


    /**
     * get all object dependent this block_id
     *
     * @param $block_id:
     *
     * @return array(obj..)
     */
    public function getAllIdsDependentBlock($block_id) {
        $model_type = $this->owner->getObjType();
        return $this->getDepeds($model_type,'block',$block_id);
    }

    /**
     * get all object dependent this category_id
     *
     * @param $category_id:
     *
     * @return
     */
    public function getAllIdsDependentCategory($category_id) {
        $model_type = $this->owner->getObjType();
        return $this->getDepeds($model_type,'category',$category_id);
    }

    /**
     * get object depend block ids.
     *
     *
     * @return
     */
    public function getCurrentDepBlockIds() {
        $model_type = $this->owner->getObjType();
        $model_id  = $this->owner->id;
        return $this->getDeps($model_type,$model_id,'block');
    }


    /**
     * update dependence of model
     *
     * @param $model_type:
     * @param $model_id:
     * @param $dep_type:
     * @param $dep_ids:
     *
     * @return
     */
    protected function updateDependence($model_type,$model_id,$dep_type,$dep_ids) {
        // get current dependence
        $current = $this->getDeps($model_type,$model_id,$dep_type);

        // calculate need to delete
        $to_del = array_diff($current, $dep_ids);
        // calculate need to insert
        $to_insert = array_diff($dep_ids, $current);

        // save to db
        if (!empty($to_del)) {
            $this->removeDeps($model_type,$model_id,$dep_type,$to_del);
        }
        if (!empty($to_insert)) {
            $this->addDeps($model_type,$model_id,$dep_type,$to_insert);
        }
        return true;
    }

    /**
     * function_description
     *
     *
     * @return
     */
    protected function addDeps($model_type,$model_id,$dep_type,$dep_ids) {
        if (empty($dep_ids)) {
            return true;
        }
        $sql = "INSERT INTO " . $this->tableName . " (obj_type,obj_id,dep_type,dep_id) VALUES (:obj_type,:obj_id,:dep_type,:dep_id)";
        $cmd = $this->getDbConnection()->createCommand($sql);
        if (!is_array($dep_ids)) {
            $dep_ids = array($dep_ids);
        }
        $cmd->bindParam(":obj_type", $model_type);
        $cmd->bindParam(":obj_id", $model_id);
        $cmd->bindParam(":dep_type", $dep_type);
        foreach ($dep_ids as $id) {
            if(empty($id)){
                continue;
            }
            $cmd->bindParam(":dep_id", $id);
            $cmd->execute();
        }
        return true;
    }


    /**
     * function_description
     *
     * @param $model_type:
     * @param $model_id:
     * @param $dep_type:
     * @param $dep_ids:
     *
     * @return
     */
    protected function removeDeps($model_type,$model_id,$dep_type,$dep_ids) {
        if (empty($dep_ids)) {
            return true;
        }

        $sql="DELETE FROM " . $this->tableName .
        " WHERE obj_type=:obj_type AND dep_type=:dep_type AND obj_id=:obj_id ";
        $sql .= " AND dep_id in ('".implode("','",$dep_ids)."')";
        if (!is_array($dep_ids)) {
            $dep_ids = array($dep_ids);
        }
        $cmd = $this->getDbConnection()->createCommand($sql);
        $cmd->bindParam(":obj_type", $model_type);
        $cmd->bindParam(":obj_id", $model_id);
        $cmd->bindParam(":dep_type", $dep_type);
        return $cmd->execute();
    }


    /**
     * get objects that the model dependent.
     *
     * @param $model_type:
     * @param $model_id:
     * @param $dep_type:
     *
     * @return
     */
    protected function getDeps($model_type, $model_id, $dep_type) {
        $query = new Query;
        $rows = $query
                     ->select('dep_id')
                     ->from($this->tableName)
                     ->where(array('and',
                             'obj_type=:obj_type',
                             'dep_type=:dep_type',
                             'obj_id=:obj_id',
                         ),
                         array(
                             ':obj_type'=>$model_type,
                             ':obj_id'=>intval($model_id),
                             ':dep_type'=>$dep_type
                         ))
                     ->all($this->getDbConnection());
        return array_map(function($a){return $a['dep_id'];},$rows);
    }

    /**
     * get all models depend this dep_id
     *
     * @param $model_type:
     * @param $dep_type:
     * @param $dep_id:
     *
     * @return
     */
    protected function getDepeds($model_type, $dep_type, $dep_id) {
        $query = new Query;
        $rows = $query
                     ->select('obj_id')
                     ->from($this->tableName)
                     ->where(array('and',
                             'obj_type=:obj_type',
                             'dep_type=:dep_type',
                             'dep_id=:dep_id',
                         ),
                         array(
                             ':obj_type'=>$model_type,
                             ':dep_type'=>$dep_type,
                             ':dep_id' => $dep_id,
                         ))
                     ->all($this->getDbConnection());
        return array_map(function($a){return $a['obj_id'];},$rows);

    }



    /**
     * return the database connection used by this behavior.
     *
     *
     * @return
     */
    public function getDbConnection() {
        // if ($this->db==null) {
            $this->db = \Yii::$app->get($this->conn_id);
            // if (!($this->db instanceof CDbConnection)) {
            //     throw new CDbException('CmsDepBehavior requires a "db" CDbConnection applicaton component.');
            // }
        // }
        return $this->db;
    }


}