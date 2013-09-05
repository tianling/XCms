<?php
/**
 * @author lancelot <cja.china@gmail.com>
 * Date 2013-9-5
 * Encoding GBK 
 */
class AddController extends CmsController{
	/**
	 * 
	 * @var AuthAssigner
	 */
	public $assigner;
	
	public function init(){
		$this->assigner = Yii::app()->getAuthManager()->getAssigner();
	}
	
	public function filters(){
		return array();
	}
	
	public function actionIndex(){
		//$this->assigner->grant(AuthAssigner::ITEM_PERMISSION, array())->to(AuthAssigner::ITEM_ROLE)->doit();
		$auth = Yii::app()->getAuthManager();
		$operation = array(
				'module' => 'access3',
				'controller' => 'access3',
				'action' => 'access3'
		);
		$r = $auth->checkAccess($operation,35);
	}
}