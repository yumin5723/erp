<?php

class BlockBackup extends CActiveRecord {

    /**
     * function_description
     *
     * @param $className:
     *
     * @return
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function tableName() {
        return 'block_backup';
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function rules() {
        return array(
            array('block_id,content','safe'),
        );
    }

    /**
     * function_description
     *
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
             'block'=>array(self::BELONGS_TO, 'Block', 'block_id'),
        );
    }
    public function behaviors()
    {
        return CMap::mergeArray(
            parent::behaviors(),
            array(
              'CTimestampBehavior' => array(
                    'class'               => 'zii.behaviors.CTimestampBehavior',
                    'createAttribute'     => 'created',
                    'updateAttribute'     => 'modified',
                    'timestampExpression' => 'date("Y-m-d H:i:s")',
                    'setUpdateOnCreate'   => true,
                ),
           )
        );
    }
    /**
     * get all block history records
     * @param  intval $block_id [description]
     * @return [type]           [description]
     */
    public function getAllUpdateRecordsById($block_id){
        $criteria=new CDbCriteria;
        $criteria->condition = "block_id = :block_id";
        $criteria->params = array(":block_id"=>$block_id);


        return new CActiveDataProvider( $this, array(
                'criteria'=>$criteria,
            ) );
    }
}