<?php
namespace common\extensions\Twig;

use Yii;
use yii\widgets\ActiveForm;
 
class GTwigExtension extends \Twig_Extension
{
    /**
    * {@inheritdoc}
    */
    public function getName()
    {
        return 'gTwigExtension';
    }
     
     
    /**
    * {@inheritdoc}
    */
    public function getGlobals()
    {
        if(Yii::$app->id == "app-console"){
            return array(
                // 'ActiveForm'=>\yii\widgets\ActiveForm,
                'App' =>Yii::$app,
                'staticUrl'=>Yii::$app->params['targetDomain'],
                 // 'u' => Yii::$app->user->isGuest ? null : \common\components\UserInfo::factory(Yii::$app->user->id),
            );
        }
        return array(
            // 'ActiveForm'=>\yii\widgets\ActiveForm,
            'App' =>Yii::$app,
            'staticUrl'=>Yii::$app->params['targetDomain'],
             'u' => Yii::$app->user->isGuest ? null : \common\components\UserInfo::factory(Yii::$app->user->id),
        );
    }
     
     
    /**
    * {@inheritdoc}
    */
    public function getFunctions()
    {
        return array(
            "widget" => new \Twig_Function_Method($this, 'widget', array('is_safe'=>['html'])),
        );
    }
    /**
    * {@inheritdoc}
    */
    public function getFilters()
    {
        return array(
            "value_callback"=> new \Twig_Filter_Method($this,'eval_string',array()),
            "cut"=> new \Twig_Filter_Method($this,'cutstr',array()),
        );
    }

    public function eval_string($s){
        return eval('return function($model,$index,$column_data){'.$s.'};');
    }

    public function widget($viewName, array $config) 
    {
        return $viewName::widget($config);
    }
    /**
     * sub str
     * @param  [type] $string [description]
     * @param  [type] $length [description]
     * @param  string $etc    [description]
     * @return [type]         [description]
     */
    function cutstr($string,$length,$etc="..."){
        $result = '';
        $string = html_entity_decode(trim(strip_tags($string)), ENT_QUOTES, 'UTF-8');
        $strlen = strlen($string);
        for ($i = 0; (($i < $strlen) && ($length > 0)); $i++){
            if ($number = strpos(str_pad(decbin(ord(substr($string, $i, 1))), 8, '0', STR_PAD_LEFT), '0')){
                if ($length < 1.0){
                    break;
                }
                $result .= substr($string, $i, $number);
                $length -= 1.0;
                $i += $number - 1;
            }else{
                $result .= substr($string, $i, 1);
                $length -= 0.5;
            }
        }
        $result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
        if ($i < $strlen){
            $result .= $etc;
        }
        return $result;
    }
     
}