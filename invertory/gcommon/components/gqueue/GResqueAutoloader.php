<?php
/**
 * This file part of RResque
 *
 * Autoloader for Resque library
 *
 * For license and full copyright information please see main package file
 * @package       yii-resque
 */
class GResqueAutoloader
{
    /**
     * Registers Raven_Autoloader as an SPL autoloader.
     */
    static public function register()
    {
        spl_autoload_unregister(['Yii','autoload']);
        spl_autoload_register([new self,'autoload']);
        spl_autoload_register(['Yii','autoload'],true,true);


        // Yii::registerAutoloader(array(new self,'autoload'),true);
    }

    /**
     * Handles autoloading of classes.
     *
     * @param  string  $class  A class name.
     *
     * @return boolean Returns true if the class has been loaded
     */
    static public function autoload($class)
    {
        if (is_file($file = dirname(__FILE__).'/lib/'.str_replace(array('_', "\0"), array('/', ''), $class).'.php')) {
            require $file;
        } else if (is_file($file = dirname(__FILE__).'/lib/ResqueScheduler/'.str_replace(array('_', "\0"), array('/', ''), $class).'.php')) {
            require $file;
        } else if (is_file($file = dirname(__FILE__).'/'.str_replace(array('_', "\0"), array('/', ''), $class).'.php')) {
            require $file;
        } else if (is_file($file = dirname(__FILE__).'/lib/'.str_replace(array('\\', "\0"), array('/', ''), $class).'.php')) {
            require $file;
        }
    }
}