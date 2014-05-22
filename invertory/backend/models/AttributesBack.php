<?php
namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use common\models\Attributes;
use yii\data\ActiveDataProvider;

class AttributesBack extends Attributes{

    private $_goodsTypeName = [];
    public $goods_type_id; //类型表（goods_type）ID
    public $attribute_id; //属性表（attribute）ID

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['add_date'],
                    // ActiveRecord::EVENT_BEFORE_UPDATE => 'last_update',
                ],
            ],
        ];
    }
	/**
	 * [rules description]
	 * @return array [type] [description]
	 */
	public function rules(){
		return [
            ['cp_id','required'],
            ['type_id','required'],
			['attr_name','required'],
            ['attr_status','required'],
			['attr_type','required'],
			['sort_order','required'],
		];
	}


    /**
     * [attributeLabels description]
     * @return array [type] [description]
     */
    public function attributeLabels(){
		return [
            'cp_id' => '商家',
			'attr_name'=>'属性名称',
            'type_id'=>'属性分类',
			'attr_type'=>'属性类型',
			'attr_status'=>'属性状态',
			'sort_order'=>'排序',
		];
	}

    /**
     * 添加商品属性
     * @param $params array 添加数据
     * @return bool true 添加成功 false 添加失败
     */
    /*public function addAttributes($params)
    {
        $formName = $this->formName();
        $data = $params["$formName"];
        var_dump($_POST);die;
        //获取商品的属性数据
        $attribute = $params['block'];
        //获取商品的属性值数据
        $attributeValue = array_values($params['addAttr']);

        //开启事务处理
        $transaction = static::getDb()->beginTransaction();

        //批量添加属性
        foreach($attribute as $value){
            $data['attr_name'] = $value;
            $model = new AttributesBack();
            $model->setAttributes($data);
            $model->save();
            $id = $model->attr_id;
            if($id){
                $attrId[] = $id;
            }else{
                $transaction->rollBack(); //添加失败
                return false;
            }
        }
        $attributeValueObj = new AttributeValueBack();
        $result = $attributeValueObj->addAttributeValue($attrId,$attributeValue);
        if(!$result){
            $transaction->rollBack(); //添加失败
            return false;
        }
        $transaction->commit();
        return true;
    }*/

    /**
     * 以数组形式返回根据商品ID查询的属性数据
     * @param $goodsId 属性ID
     * @return bool
     */
    /*public static function findAttributesArray($goodsId)
    {
        $attributes = static::find()
                    ->where(["goods_type" => "$goodsId"])
                    ->asArray()
                     ->all();
        if($attributes){
            return $attributes;
        }else{
            return false;
        }
    }*/


    /**
     * 根据属性分类获取对应的属性
     * @return ActiveDataProvider
     */
    public function selectAttribute()
    {
        //拼装条件
        $type_id = $this->goods_type_id;
        $where = ['type_id'=> "$type_id",'attr_status'=>'1'];
        //如果值是－1则获取全部属性
        if($type_id === -1){
            $where = ['attr_status'=>'1'];
        }
        $provider = new ActiveDataProvider([
            'query' => static::find(),
                    // ->where($where)
                    // ->orderBy('attr_type ASC , sort_order DESC'),
            'sort' => false,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        return $provider;
    }


    /**
     * 获取一条属性
     * @return \yii\db\ActiveQuery|\yii\db\ActiveQueryInterface
     */
    public function findAttribute()
    {
        $result = static::find($this->attribute_id);
        return $result;
    }


    /**
     * 根据分类ID查询相关数据 并且以数组形式返回
     * @param $type 对应goods_type 表的ID
     * @return array
     */
    public function findAttributeArray($type)
    {
        $result = static::find()
                ->select('attr_id,type_id,attr_name,attr_type')
                ->where(['type_id'=>$type , 'attr_status'=>'1'])
                ->orderBy(' attr_type ASC , sort_order DESC ')
                ->asArray()
                ->all();
        return $result;
    }

    /**
     * 添加属性
     * @param $params array 需要添加的数据
     * @return mixed
     * [
     *  status 操作状态 1 成功 0 失败
     *  type_id 属性对应的类型表（goods_type）ID
     *  hint 状态提示
     * ]
     */
    public function addAttributes($params)
    {
        if($this->load($params) && $this->save()){
            $message['status'] = 1;
            $message['type_id'] = $this->type_id;
            $message['hint'] = '添加属性成功';
        }else{
            $message['status'] = 0;
            $message['hint'] = '添加属性失败';
        }
        return $message;
    }


    /**
     * 修改属性
     * @param $params array 需要修改的属性
     * @return array
     * [
     *  status 操作状态 1 成功 0 失败
     *  type_id 属性对应的类型表（goods_type）ID
     *  hint 操作提示
     * ]
     */
    public function updateAttribute($params)
    {
        $post = static::find($this->attribute_id);
        $message = [];
        if($post->load($params) && $post->save()){
            $message['status'] = 1;
            $message['type_id'] = $post->type_id;
            $message['hint'] = ' 修改成功';
        }else{
            $message['status'] = 0;
            $message['hint'] = ' 修改失败';
        }

        return $message;
    }


    /**
     * 删除属性 因为属性是不能真正的删除 所以把不要的属性做隐藏操作 隐藏后不再显示
     * @return array
     * [
     *  status 操作状态 1 成功 0 失败
     *  type_id 属性对应的类型表（goods_type）ID
     *  hint 操作提示
     * ]
     */
    public function deleteAttribute()
    {
        $model  = static::find($this->attribute_id);
        $model->attr_status = 2;
        $message = [];
        $message['type_id'] = $model->type_id;
        if($model->save()){
            $message['status'] = 1;
            $message['hint'] = '删除属性成功';
        }else{
            $message['status'] = 0;
            $message['hint'] = '删除属性失败';
        }
        return $message;
    }


    /**
     * 获取属性类型
     * @return callable
     */
    public function attributeTypeName(){
        return function($data){
            if($data->attr_type === 1){
                return '商品属性';
            }elseif($data->attr_type === 2){
                return '产品参数';
            }
        };
    }


    /**
     * 获取属性对应的分类
     * @return callable
     */
    public function goodsTypeName()
    {
        return function($data){
            if(!empty($this->_goodsTypeName[$data->type_id])){
                return $this->_goodsTypeName[$data->type_id];
            }
            $model = GoodsTypeBack::find($data->type_id);
            $this->_goodsTypeName[$data->type_id] = $model->type_name;
            return $model->type_name;
        };
    }


    /**
     * 返回属性的类型
     * @return array
     */
    public function attributeType()
    {
        return[
            '1' => '商品属性',
            '2' => '产品参数',
        ];
    }

    /**
     * 返回属性的分类
     * @return mixed
     */
    public function attributeCategory()
    {
        $array = GoodsTypeBack ::find()
                ->select(['type_id','type_name','type_group'])
                ->where(['is_show' => 1])
                ->asArray()
                ->all();
        if(!empty($array)){
            $category = $this->unlimitedForLevel($array,'type_id','type_group');
            foreach($category as $value){
                $cat[$value['type_id']] = $value['html'].$value['type_name'];
            }
            return $cat;
        }
    }


    /**
     * 对所以的商品分类进行整理排序
     * @param $category 需要整理的分类数据
     * @param $id $category分类代表id 的 key
     * @param $fid $category分类代表父id 的 key
     * @param string $html 父类与子类的区别符号
     * @param int $pid 父类ID
     * @param int $level 分类等级
     * @return array  整理完成的分类数据
     */
    private function unlimitedForLevel($category,$id,$fid,$html = " -- ",$pid = 0,$level = 0){
        $array = array();
        foreach($category as $val){
            if($val[$fid] == $pid){
                $val['level'] = $level+1;
                $val['html'] = str_repeat($html,$level);
                $array[] = $val;
                $array = array_merge($array,$this->unlimitedForLevel($category,$id,$fid,$html,$val[$id],$level+1));
            }
        }
        return $array;
    }
}