<?php
namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;
// use common\extensions\NestedSetBehavior;
use yii\helpers\BaseArrayHelper;
use common\models\Category;

class CategoryBack extends Category {
    const ERROR_ATTRIBUTE_VALUE = 3001;

    const ERROR_NOT_ALLOW_ATTRIBUTE = 3002;

    const ERROR_NOTHING_TO_MODIFY = 3003;

    const ERROR_UNKNOW = 3004;

    const ERROR_NOT_FOUND = 3005;

    const ERROR_SOME_NOT_FOUND = 3006;
    
    const PRODUCT_CATEGORY = 43;
    
    public static function tableName(){
        return "category";
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function rules(){
        return [
            ['name','required'],
            ['short_name','safe'],
            ['description','safe'],
        ];
    }

    /**
     * function_description
     *
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
        );
    }


    public function behaviors()
    {
        return BaseArrayHelper::merge(
            parent::behaviors(),
            [
              'nestedSetBehavior' => [
                'class' => 'common\extensions\NestedSetBehavior',
                'leftAttribute'=>'left_id',
                'rightAttribute'=>'right_id',
                'levelAttribute'=>'level',
              ],
              'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created', 'modified'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'modified',
                ],
                // 'timestamp' => function (){ return date("Y-m-d H:i:s");}
            ],
           ]
        );
    }
    /**
     * create and save new category
     *
     *
     * @return array(boolean result, MError err)
     * result for if new category have created and saved success.
     * err is the error for not created or saved. if saved success, the err is null
     */
    public function setCategory($category_id,$uid){
        if($category_id==0){
            $root = CategoryBack::findOne(['root'=>$category_id]);
        }else{
            $root = CategoryBack::findOne($category_id);
        }
        $model = new self;
        $model->name = $this->name;
        $model->short_name = $this->short_name;
        $model->description = $this->description;
        $model->admin_id = $uid;
        if($model->appendTo($root)){
            return array(true, null);
        }
    }


    public function changeCategory($id,$uid){
        $model = CategoryBack::findOne($id);
        $model->name = $this->name;
        $model->short_name = $this->short_name;
        $model->description = $this->description;
        $model->admin_id = $uid;
        if($model->saveNode()){
            return true;
        }return false;
    }


    public function getAllRoots(){
        $model = new CategoryBack; 
        $roots=$model->roots();
        $results = new ActiveDataProvider([
            'query' => $roots,
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);
        return $results;
    }


    public function getAllData(){
        $rootId = $_GET['root'];
        $root = CategoryBack::findOne(["root"=>$rootId]);
        $provider = [];
        if($root){
            $descendants = $root->descendants();
            $provider = new ActiveDataProvider([
                'query' => $descendants,
                'sort' => false,
                'pagination' => [
                    'pageSize' => 20,
                ],
            ]);
        }
        return $provider;
    }


    public function saveRootNode($params){
        $model = new CategoryBack;
        $model->name = $params['name'];
        $model->short_name = $params['short_name'];
        $model->saveNode();
    }


    public static function getCatIds(){
        $model = new CategoryBack;
        $roots = $model->findOne(['root'=>0]);
        $descendants = $roots->descendants()->all();
        $rs = ['1'=>'请选择分类'];
        foreach ($descendants as $key => $value) {
            $rs[$value['id']] = static::str_tree($value['level'],$value['name']);
        }
        return $rs;
    }


    public static function str_tree($level,$name){
        $nav = '|';
        $result = str_repeat("-", $level*pow(2, 3));
        
        return $nav.$result.$name."(".($level-1)."级分类)";
    }


    public function attributelabels(){
        return [
            'name'=>'分类名称',
            'short_name'=>'分类缩写',
            'description'=>'描述',
        ];
    }
    /**
     *  add "|-" for view category
     * 
     */
    public function  cate_tree(){
        return function ($data){
            $nav = '|';
            $result = str_repeat("-", $data->level*pow(2,3));
            return $nav.$result.$data->name."(".($data->level-1)."级分类)";
        };
    }


    public function updateCategory($id){
        $post = static::findOne($id);
        if (!$post) {
            return false;
        }
        if (\Yii::$app->request->isPost) {
            $post->load(Yii::$app->request->post());
            if ($post->saveNode()) {
                return true;
            }
        }
        return false;
    }


    public function getUrl(){
        return function ($data){
            return "ddf";
        };
    }
   
}