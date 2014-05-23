<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace common\extensions\Grid;

use Yii;
use yii\helpers\Html;
use yii\grid\ActionColumn;

/**
 * ActionColumn is a column for the [[GridView]] widget that displays buttons for viewing and manipulating the items.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class GridActionColumn extends ActionColumn
{
    /**
     * Initializes the default button rendering callbacks
     */
    protected function initDefaultButtons()
    {
        if (!isset($this->buttons['updatekey'])) {
            $this->buttons['updatekey'] = function ($url, $model) {
                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                    'title' => Yii::t('yii', 'update friend link'),
                ]);
            };
        }
        if (!isset($this->buttons['update'])) {
            $this->buttons['update'] = function ($url, $model) {
                return Html::a('<i class="fa fa-pencil"></i>', $url, [
                    'title' => Yii::t('yii', 'update'),
                ]);
            };
        }
        if (!isset($this->buttons['delete'])) {
            $this->buttons['delete'] = function ($url, $model) {
                return Html::a('<i class="glyphicon glyphicon-trash"></i>', $url, [
                    'title' => Yii::t('yii', 'delete'),
                    'data-confirm' => Yii::t('yii', 'Are you sure to delete this item?'),
                    'data-method' => 'post',
                ]);
            };
        }
        if (!isset($this->buttons['deletekey'])) {
            $this->buttons['deletekey'] = function ($url, $model) {
                return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                    'title' => Yii::t('yii', 'Delete'),
                    'data-confirm' => Yii::t('yii', 'Are you sure to delete this item?'),
                    'data-method' => 'post',
                ]);
            };
        }
        if (!isset($this->buttons['delete-link'])) {
            $this->buttons['delete-link'] = function ($url, $model) {
                return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                    'title' => Yii::t('yii', 'Delete'),
                    'data-confirm' => Yii::t('yii', 'Are you sure to delete this item?'),
                    'data-method' => 'post',
                ]);
            };
        }
        if (!isset($this->buttons['update-link'])) {
            $this->buttons['update-link'] = function ($url, $model) {
                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                    'title' => Yii::t('yii', 'update friend link'),
                ]);
            };
        }
        if (!isset($this->buttons['addlink'])) {
            $this->buttons['addlink'] = function ($url, $model) {
                return Html::a('<span class="glyphicon glyphicon-plus"></span>', $url, [
                    'title' => Yii::t('yii', 'add goods attribute'),
                ]);
            };
        }
        if (!isset($this->buttons['updatelinkblock'])) {
            $this->buttons['updatelinkblock'] = function ($url, $model) {
                if(\Yii::$app->user->id != 1){
                    return '';
                }
                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                    'title' => Yii::t('yii', 'update link block name'),
                ]);
            };
        }
        if (!isset($this->buttons['assignment'])) {
            $this->buttons['assignment'] = function ($url, $model) {
                return Html::a('<i class="icon-cog"></i>', "/auth/assignment?user_id=$model->id", [
                    'title' => Yii::t('yii', 'assignment roles to user'),
                ]);
            };
        }

        if (!isset($this->buttons['assign'])) {
            $this->buttons['assign'] = function ($url, $model) {
                return Html::a('<span class="glyphicon glyphicon-cog"></span>', $url, [
                    'title' => Yii::t('yii', 'assign user and roles permission'),
                ]);
            };
        }
        if (!isset($this->buttons['show'])) {
            $this->buttons['show'] = function ($url, $model) {
                return Html::a('<span class="glyphicon glyphicon-cog"></span>', $url, [
                    'title' => Yii::t('yii', 'show goods status'),
                ]);
            };
        }
        if (!isset($this->buttons['check'])) {
            $this->buttons['check'] = function ($url, $model) {
                return Html::a('<span class="glyphicon glyphicon-cog"></span>', $url, [
                    'title' => Yii::t('yii', 'check goods status'),
                ]);
            };
        }
        if (!isset($this->buttons['updatest'])) {
            $this->buttons['updatest'] = function ($url, $model) {
                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                    'title' => Yii::t('yii', 'update goods status'),
                ]);
            };
        }
        if (!isset($this->buttons['addmore'])) {
            $this->buttons['addmore'] = function ($url, $model) {
                return Html::a('<span class="glyphicon glyphicon-plus"></span>', $url, [
                    'title' => Yii::t('yii', 'add goods attribute'),
                ]);
            };
        }
        if (!isset($this->buttons['adetail'])) {
            $this->buttons['adetail'] = function ($url, $model) {
                return Html::a('<span class="glyphicon glyphicon-plus"></span>', $url, [
                    'title' => Yii::t('yii', 'add label ad details'),
                ]);
            };
        }
        if (!isset($this->buttons['updetail'])) {
            $this->buttons['updetail'] = function ($url, $model) {
                return Html::a('<span class="glyphicon glyphicon-edit"></span>', $url, [
                    'title' => Yii::t('yii', 'add label ad details'),
                ]);
            };
        }
        if (!isset($this->buttons['deldetail'])) {
            $this->buttons['deldetail'] = function ($url, $model) {
                return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                    'title' => Yii::t('yii', 'Delete'),
                    'data-confirm' => Yii::t('yii', 'Are you sure to delete this item?'),
                    'data-method' => 'post',
                ]);
            };
        }

        if (!isset($this->buttons['viewdetail'])) {
            $this->buttons['viewdetail'] = function ($url, $model) {
                return Html::a('<span class="glyphicon glyphicon-flag"></span>', $url, [
                    'title' => Yii::t('yii', '查看详情'),
                ]);
            };
        }
        /**
         * 类型添加属性按钮
         */
        if (!isset($this->buttons['addattribute'])) {
            $this->buttons['addattribute'] = function ($url, $model) {
                if($model->type_group === 0){
                    return '';
                }
                return Html::a('<span class="glyphicon glyphicon-plus"></span>', 'attribute/addattribute?id='.$model->type_id, [
                    'title' => Yii::t('yii', '添加属性'),
                ]);
            };
        }

        if (!isset($this->buttons['deleteattribute'])) {
            $this->buttons['deleteattribute'] = function ($url,$model) {
                return Html::a('<span class="glyphicon glyphicon-trash"></span>',$url,[
                    'title' => Yii::t('yii', '删除属性'),
                    'data-confirm' => Yii::t('yii', '确定要删除该属性'),
                ]);
            };
        }


        /**
         * 商品操作按钮
         */
        if (!isset($this->buttons['add-attribute-value'])) {
            $this->buttons['add-attribute-value'] = function ($url, $model) {
                return Html::a('<span class="glyphicon glyphicon-plus"></span>', 'attribute-value/add?id='.$model->goods_id.'&type_id='.$model->type_id, [
                    'title' => Yii::t('yii', '添加商品属性值'),
                ]);
            };
        }
        if (!isset($this->buttons['update-attribute-value'])) {
            $this->buttons['update-attribute-value'] = function ($url, $model) {
                return Html::a('<span class="glyphicon glyphicon-edit"></span>', 'attribute-value/update?id='.$model->goods_id.'&type_id='.$model->type_id, [
                    'title' => Yii::t('yii', '修改商品属性值'),
                ]);
            };
        }

        if (!isset($this->buttons['add-sku-value'])) {
            $this->buttons['add-sku-value'] = function ($url, $model) {
                return Html::a('<span class="glyphicon glyphicon-plus"></span>', 'goods-sku/add?id='.$model->goods_id.'&type_id='.$model->type_id, [
                    'title' => Yii::t('yii', '添加商品SKU'),
                ]);
            };
        }
        if (!isset($this->buttons['update-sku-value'])) {
            $this->buttons['update-sku-value'] = function ($url, $model) {
                return Html::a('<span class="glyphicon glyphicon-edit"></span>', 'goods-sku/update?id='.$model->goods_id.'&type_id='.$model->type_id, [
                    'title' => Yii::t('yii', '修改商品SKU'),
                ]);
            };
        }

        parent::initDefaultButtons();
        
    }
}
