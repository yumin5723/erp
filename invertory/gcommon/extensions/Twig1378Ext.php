<?php
class Twig1378Ext extends Twig_Extension {
    /**
     * function_description
     *
     *
     * @return
     */
    public function getTokenParsers() {
        return array();
    }
    /**
     * function_description
     *
     *
     * @return
     */
    public function getGlobals() {
        return array(
            'u' => Yii::app()->user->isGuest ? null : UserInfo::factory(Yii::app()->user->id),
        );
    }
    /**
     * function_description
     *
     *
     * @return
     */
    public function getFunctions() {
        return array();
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getFilters() {
        return array();
    }
    /**
     * function_description
     *
     *
     * @return
     */
    public function getName() {
        return "n131sns";
    }
}