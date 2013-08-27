<?php
/**
 * @name CmsController.php UTF-8
 * @author ChenJunAn<lancelot1215@gmail.com>
 * 
 * Date 2013-8-8
 * Encoding UTF-8
 */
class CmsController extends CController{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();
	/**
	 * @var CmsApplication
	 */
	public $app;
	/**
	 * when a action is defined as a CAction
	 * then that action's name is his name and this subfix
	 * @var string $actionSubfix used in @method self::actions()
	 */
	public $actionClassSubfix = 'Action';
	/**
	 * @var string $actionClassFolder used in @method self::actions()
	 */
	public $actionClassPathAlias = 'application.controllers';
	
	public function init(){
		$this->app = Yii::app();
	}
	
	/**
	 * @return array
	 */
	public function actions(){
		$actions = $this->getActionClass();
		$folderAlias = "{$this->actionClassPathAlias}.{$this->id}";
	
		foreach( $actions as $name => $option ){
			if ( is_array($option) ){
				$option['class'] = "{$folderAlias}.{$option['class']}{$this->actionClassSubfix}";
				$actions[$name] = $option;
			}else{
				$actions[$option] = "{$folderAlias}.{$option}{$this->actionClassSubfix}";
			}
		}
	
		return $actions;
	}
	
	/**
	 * @return array
	 */
	public function getActionClass(){
		return array();
	}
}