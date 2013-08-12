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