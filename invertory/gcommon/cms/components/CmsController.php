<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class CmsController extends CController
{
	public $layout = "//layouts/base";
	public $pageHint = "";
	public $menu = array();
	public $breadcrumbs = array();
}