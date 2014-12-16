<?php
Yii::import("gcommon.cms.models.Term");
Yii::import("gcommon.cms.models.Taxonomy");
Yii::import("gcommon.cms.models.Oterm");
Yii::import("gcommon.cms.models.ObjectTerm");
Yii::import("backend.models.Tree");
class CategoryMigration extends CConsoleCommand {
    /**
     * function_description
     *
     * @param $args:
     *
     * @return
     */
    public function run($args) {
        $this->getRootFromTaxonomy();
        $this->getAllTermId(0);
        $this->updateObjectTermId();

    }


    /*
     * get root from taxonomy
     */
    public function getRootFromTaxonomy(){
        $taxonomy = new Taxonomy;
        $taxonomy_id = $taxonomy->find();
        $this->moveRoot($taxonomy_id);
    }
    /**
     * get all term_id
     * @return array terms
     */
    public function getAllTermId($term_id){
        $gametaxonomy = new Taxonomy;
        $gameterm = new Term;
        $taxonomy_id = $gametaxonomy->find()->taxonomy_id;
        $categories = $gameterm->findAll('taxonomy_id = :id',array(':id'=>$taxonomy_id));
        $newArray = array();
        foreach($categories as $key => $ca){
            $newArray[$key]['term_id'] = $ca->term_id;
            $newArray[$key]['parent'] = $ca->parent;
            $newArray[$key]['name'] = $ca->name;
            $newArray[$key]['description'] = $ca->description;
            $newArray[$key]['slug'] = $ca->slug;
            $newArray[$key]['url'] = $ca->url;
            $newArray[$key]['taxonomy_id'] = $ca->taxonomy_id;
        }
        $tree = new Tree($newArray);
        $arr = $tree->leaf($term_id);
        $this->insertDB($arr);
    }
    /**
     * move root to new category
     * @param  [type] $array [description]
     * @return [type]        [description]
     */
    private function moveRoot($root){
        $model = new Oterm;
        $model->id = $root->taxonomy_id;
        $model->name = $root->name;
        $model->description = $root->description;
        $model->admin_id = 1;
        $model->saveNode(false);
    }
    private function insertDB($array){
        foreach ($array as $key => $value) {
            $cate = new Oterm;
            $root = $cate->findByPk($value['parent']+1);
            $cate->id = $value['term_id']+1;
            $cate->root = $value['taxonomy_id'];
            $cate->name = $value['name'];
            $cate->short_name = $value['slug'];
            $cate->description = $value['description'];
            $cate->url = $value['url'];
            $cate->admin_id = 1;
            if($cate->appendTo($root)){
                echo $cate->id."|".$value['name']."分类迁移完成</p>\n";
                if (isset($value['child'])) {
                    $this->insertDB($value['child']);
                }
            }
        }
    }

    /**
     * function_description
     *
     * @param $page_id:
     *
     * @return
     */
    public function updateObjectTermId() {
        // Game::model()->updateAll(array('category_id'=> 'category_id'+1));
        $update = Yii::app()->db->createCommand()
                    ->update('object_term', 
                            array(
                            'term_id' =>new CDbExpression("term_id + 1"),
                            )
                    );
        if ($update > 0) {
            echo "modify".$update."条数据\n";
        } else {
            echo "modify fail";
        }
    }
}