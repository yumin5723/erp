<?php
namespace gcommon\cms\components;
use Yii;
/**
 * Class defined all the Constant value of the CMS.
 * 
 * 
 * @author Tuan Nguyen
 * @version 1.0
 * @package common.components
 */

class ConstantDefine{        

    const AJAX_BLOCK_SEPERATOR='---';    

    public static function fileTypes(){
        return array(
            'image'=>array('jpg','gif','png','bmp','jpeg'),
            'audio'=>array('mp3','wma','wav'),
            'video'=>array('flv','wmv','avi','mp4','mov','3gp','swf'),
            'file'=>array('*'),           
            );
    }
    
    public static function chooseFileTypes(){
        return array(
            'auto'=>Yii::t('cms','Auto detect'),
            'image'=>Yii::t('cms','Image'),
            'video'=>Yii::t('cms','Video'),
            'audio'=>Yii::t('cms','Audio'),
            'file'=>Yii::t('cms','File'),
        );
    }
    
    
    
    /**
     * Constant related to User
     */
    const USER_ERROR_NOT_ACTIVE=3;    
    const USER_STATUS_DISABLED=0;
    const USER_STATUS_ACTIVE=1;
    
    
    public static function getUserStatus(){
        return array(
            self::USER_STATUS_DISABLED=>t("cms","Disabled"),
            self::USER_STATUS_ACTIVE=>t("cms","Active"));
    }
                                    
    
    /**
     * Constant block type
     * 
     */
    
    const PAGE_TYPE_SPECIAL = "special";
    const PAGE_TYPE_SUBJECT = "subject";
    const PAGE_TYPE_NONE = "none";
    public static function getPageType(){
        return array(
                 self::PAGE_TYPE_NONE=>"暂不标记",
                 self::PAGE_TYPE_SPECIAL=>"特殊页面",
                 self::PAGE_TYPE_SUBJECT=>"专题页面",
                );
    }
    /**
     * Constant page type like special or subject
     * 
     */
    
    const BLOCK_TYPE_CUSTOM = 1;
    const BLOCK_TYPE_NEWSLIST = 2;
    const BLOCK_TYPE_PICTRUELIST = 4;
    const BLOCK_TYPE_DATAREFERENCE = 5;
    public static function getBlockType(){
        return array(
                 self::BLOCK_TYPE_CUSTOM=>"customBlock",
                 self::BLOCK_TYPE_NEWSLIST=>"objectList",
                 self::BLOCK_TYPE_PICTRUELIST=>"pictureList",
                 self::BLOCK_TYPE_DATAREFERENCE=>'dataBlock',
                );
    }
    /**
     * Constant block page type like special or subject
     * 
     */
    
    const BLOCK_TYPE_INDEX = 0;
    const BLOCK_TYPE_NEWS = 1;
    public static function getBlockPageType(){
        return array(
                 self::BLOCK_TYPE_INDEX=>"狗民首页",
                 self::BLOCK_TYPE_NEWS=>"狗民资讯",
                );
    }

    /**
     * Constant related to Object
     * 
     */
    
    const OBJECT_STATUS_PUBLISHED=2;
    const OBJECT_STATUS_DRAFT=1;
    const OBJECT_STATUS_FAIL=3;
    const OBJECT_STATUS_DELETE=4;
    const OBJECT_ISTOP = 1;
    const OBJECT_ISRED = 1;
    const OBJECT_ISHOT = 1;
    
    public static function getObjectStatus(){
        return array(
                 self::OBJECT_STATUS_PUBLISHED=>"已发布",
                 self::OBJECT_STATUS_DRAFT=>"草稿",
                 self::OBJECT_STATUS_FAIL=>"发布失败",
                 self::OBJECT_STATUS_DELETE=>"删除",
                );
    }
    
    const GAME_WEIGHITS_NO = 0;
    const GAME_WEIGHTS_HEADLINES = 7;
    const GAME_WEIGHTS_RECOMMEND_ONE =1;
    const GAME_WEIGHTS_RECOMMEND_TWO =2;
    const GAME_WEIGHTS_RECOMMEND_THREE =3;
    const GAME_WEIGHTS_RECOMMEND_FOUR =4;
    const GAME_WEIGHTS_RECOMMEND_FIVE=5;
    const GAME_WEIGHTS_RECOMMEND_SIX=6;

    public static function getGameWeights(){
        return array(
                self::GAME_WEIGHITS_NO => Yii::t("cms","无"),
                self:: GAME_WEIGHTS_HEADLINES => Yii::t("cms","首页头条"),
                self:: GAME_WEIGHTS_RECOMMEND_ONE =>Yii::t("cms","首页推荐1"),
                self:: GAME_WEIGHTS_RECOMMEND_TWO =>Yii::t("cms","首页推荐2"),
                self:: GAME_WEIGHTS_RECOMMEND_THREE =>Yii::t("cms","首页推荐3"),
                self:: GAME_WEIGHTS_RECOMMEND_FOUR =>Yii::t("cms","首页推荐4"),
                self:: GAME_WEIGHTS_RECOMMEND_FIVE=>Yii::t("cms","首页推荐5"),
                self:: GAME_WEIGHTS_RECOMMEND_SIX=>Yii::t("cms","首页推荐6"),
                );
    }

    const OBJECT_ALLOW_COMMENT=1;
    const OBJECT_DISABLE_COMMENT=2;
    
    public static function getObjectCommentStatus(){
        return array(
                 self::OBJECT_ALLOW_COMMENT=>Yii::t("cms","Allow"),
                 self::OBJECT_DISABLE_COMMENT=>Yii::t("cms","Disable"),                 
                );
    }
   
    /**
     * Constant related to Transfer
     *         
     */
    const TRANS_ROLE=1;
    const TRANS_PERSON=2;
    const TRANS_STATUS=3;
    
    
     /**
     * Constant related to Menu
     *         
     */
    const MENU_TYPE_PAGE=1;
    const MENU_TYPE_TERM=2;
    const MENU_TYPE_CONTENT=5;
    const MENU_TYPE_URL=3;  
    const MENU_TYPE_STRING=4;
    const MENU_TYPE_HOME=6;
    
    public static function getMenuType(){
        return array(
                 self::MENU_TYPE_HOME=>t("cms","Link to Homepage"),
                 self::MENU_TYPE_URL=>t("cms","Link to URL"),                 
                 self::MENU_TYPE_PAGE=>t("cms","Link to Page"),
                 self::MENU_TYPE_CONTENT=>t("cms","Link to a Content Object"),
                 self::MENU_TYPE_TERM=>t("cms","Link to a Term Page"),                                 
                 self::MENU_TYPE_STRING=>t("cms","String"),
                );
    }
    
    
    /**
     * Constant related to Content List
     *         
     */
    const CONTENT_LIST_TYPE_MANUAL=1;
    const CONTENT_LIST_TYPE_AUTO=2;
   
    
    public static function getContentListType(){
        return array(
                 self::CONTENT_LIST_TYPE_MANUAL=>t("cms","Manual"),                 
                 self::CONTENT_LIST_TYPE_AUTO=>t("cms","Auto"),
                 
                );
    }
    
    const CONTENT_LIST_CRITERIA_NEWEST=1;
    const CONTENT_LIST_CRITERIA_MOST_VIEWED_ALLTIME=2;
   
    
    public static function getContentListCriteria(){
        return array(
                 self::CONTENT_LIST_CRITERIA_NEWEST=>t("cms","Newsest"),                 
                 self::CONTENT_LIST_CRITERIA_MOST_VIEWED_ALLTIME=>t("cms","Most viewed all time"),                 
                );
    }
    
    const CONTENT_LIST_RETURN_DATA_PROVIDER=1;
    const CONTENT_LIST_RETURN_ACTIVE_RECORD=2;
    
    public static function getContentListReturnType(){
        return array(
                 self::CONTENT_LIST_RETURN_DATA_PROVIDER=>t("cms","Data Provider"),                 
                 self::CONTENT_LIST_RETURN_ACTIVE_RECORD=>t("cms","Active Record"),                 
                );
    }
    
    /**
     * Constant related to Page
     *         
     */
    const PAGE_ACTIVE=1;
    const PAGE_DISABLE=2;
    
    public static function getPageStatus(){
        return array(
                 self::PAGE_ACTIVE=>t("cms","Active"),
                 self::PAGE_DISABLE=>t("cms","Disable"),                 
                );
    }

    /**
     * Constant related to Page
     *         
     */
    const PAGE_DISPLAY_WEB='web';
    const PAGE_DISPLAY_MOBILE='mobile';
    
    public static function getPageDisplayDevice(){
        return array(
            self::PAGE_DISPLAY_WEB=>t("cms","Web"),
            self::PAGE_DISPLAY_MOBILE=>t("cms","Mobile"),                 
        );
    }
    
    const PAGE_ALLOW_INDEX=1;
    const PAGE_NOT_ALLOW_INDEX=2;
    
    public static function getPageIndexStatus(){
        return array(
                 self::PAGE_ALLOW_INDEX=>t("cms","Allow index"),
                 self::PAGE_NOT_ALLOW_INDEX=>t("cms","Not allow Index"),                 
                );
    }
    
    const PAGE_ALLOW_FOLLOW=1;
    const PAGE_NOT_ALLOW_FOLLOW=2;
    
    public static function getPageFollowStatus(){
        return array(
                 self::PAGE_ALLOW_FOLLOW=>t("cms","Allow follow"),
                 self::PAGE_NOT_ALLOW_FOLLOW=>t("cms","Not allow follow"),                 
                );
    }
    
    
    const PAGE_BLOCK_ACTIVE=1;
    const PAGE_BLOCK_DISABLE=2;
    
    public static function getPageBlockStatus(){
        return array(
                 self::PAGE_BLOCK_ACTIVE=>t("cms","Active"),
                 self::PAGE_BLOCK_DISABLE=>t("cms","Disable"),                 
                );
    }


    public static function authTypes(){
        return array(
            CAuthItem::TYPE_OPERATION=>t('cms','Operation'),
            CAuthItem::TYPE_TASK=>t('cms','Task'),
            CAuthItem::TYPE_ROLE=>t('cms','Role'),          
        );
    }
    
    
    /**
     * Constant related to Avatar Size
     */    
    
    const AVATAR_SIZE_96=96;
    const AVATAR_SIZE_23=23;
          
    public static function getAvatarSizes(){
        return array(
            self::AVATAR_SIZE_23=>t("cms","23"),
            self::AVATAR_SIZE_96=>t("cms","96"));
    }

    /**
     * Constant term order 
     */    
    
    const TERM_ORDER_NORMAL=0;
    const TERM_ORDER_PRIMARY=1;

    public static function getTermOrder(){
        return array(
            self::TERM_ORDER_NORMAL=>Yii::t('cms','normal'),
            self::TERM_ORDER_PRIMARY=>Yii::t('cms',"primary")
        );

    }

    /**
     * Constant term order 
     */    
    
    const TEMPLETE_CONTENT_TYPE=0;
    const TEMPLETE_LIST_TYPE=1;
    const TEMPLETE_TAG_TYPE=2;
    public static function getTempleteType(){
        return array(
            self::TEMPLETE_CONTENT_TYPE=>'新闻内容页',
            self::TEMPLETE_LIST_TYPE=>"新闻列表页",
            self::TEMPLETE_TAG_TYPE=>'标签页模板',
        );

    }


    public static function getGameRelated(){
        return array(
                "1" => array(
                        10 => array(
                            "name" => "游戏相关",
                        ),
                        20 => array(
                            "name" => "运营平台",
                        ),
                    ),
                );
    }
}