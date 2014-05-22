<?php
/**
 * User: liding
 * Date: 14-4-10
 * Time: 13:25
 */

namespace backend\models;

use common\models\GoodsType;
use yii\data\ActiveDataProvider;

class GoodsTypeBack extends GoodsType{

    private $_group =[];

    public function rules(){
        return [
            ['cp_id','integer','message'=>'必须为整数！'],
            ['type_name','required'],
            ['type_group','required'],
            ['is_show','required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'type_id' => '类型ID',
            'cp_id' => '商家',
            'type_name' =>'类型名称',
            'type_group' =>'所属组',
            'is_show' =>'状态',
        ];
    }


    /**
     * 获取狗民网所有设置的商品类型
     */
    public function selectGoodsType()
    {
        $provider = new ActiveDataProvider([
            'query' => static :: find()
                        ->where(['cp_id' => 1]),
            'sort'=>false,
            'pagination'=> [
                'pageSize'=>20,
            ],
        ]);
        return $provider;
    }


    /**
     * 添加类型
     * @param $params
     * @return bool
     */
    public function addGoodsType($params)
    {
        if($this->load($params) && $this->save()){
            return true;
        }
        return false;
    }


    public function changeTypeStatus()
    {
        $type_id = $_GET['id'];
        $action = $_GET['act'];
        $model = static :: find($type_id);
        $model->is_show = $action;
        if($model->save()){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 返回所属组的名称
     * @return callable
     */
    public function groupName()
    {
        return function($data){
            //如果存在则直接返回
            if(!empty($this->_group[$data->type_group])){
                return $this->_group[$data->type_group];
            }
            $name = static :: find()->select('type_name')->where(['type_id' => $data->type_group])->asArray()->one();
            if($name){
                $this->_group[$data->type_group] = $name['type_name'];
                return $name['type_name'];
            }else{
                $this->_group[$data->type_group] = '主属性';
                return '主属性';
            }
        };
    }


    /**
     * 返回商品类型状态
     * @return callable
     */
    public function typeStatus()
    {
        return function($data){
            if($data->is_show ===1){
                return '显示';
            }elseif($data->is_show ===0){
                return '隐藏';
            }
        };
    }


    /**
     * 返回当前可操作的选项
     * @return callable
     */
    public function returnTypeStatus(){
        return function($data){
            if($data->is_show === 1){
                return " <a href='/type/change?id=$data->type_id&act=0'>隐藏</a> ";
            }elseif($data->is_show === 0){
                return " <a href='/type/change?id=$data->type_id&act=1'>显示</a> ";
            }
        };
    }


    /**
     * 返回属性组选项
     * @return bool
     */
    public function groupList(){
        $result = static :: find()
                ->select(['type_id','type_name'])
                ->where(['type_group' => '0','cp_id' => '1','is_show'=>'1'])
                ->asArray()
                ->all();
        $array[0] = '主属性';
        if($result){
            foreach($result as $value){
                $array[$value['type_id']] = $value['type_name'];
            }
            return $array;
        }
        return $array;
    }


    /**
     * 返回商家选项
     * @return array
     */
    public function cpList()
    {
        return ['1' => '狗民网'];
    }


    /**
     * 返回商家
     * @return callable
     */
    public function returnCp()
    {
        return function($data){
            if($data->cp_id === 1){
                return '狗民网';
            }
        };
    }
} 