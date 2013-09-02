<?php
/**
 * @name HasLogined.php UTF-8
 * @author ChenJunAn<lancelot1215@gmail.com>
 * 
 * Date 2013-9-3
 * Encoding UTF-8
 */
class HasLogined extends CFilter{
	
	public function preFilter($filterChain){
		if ( Yii::app()->getUser()->getIsGuest() === true ){
			$filterChain->controller->loginRequired();
			return false;
		}else {
			return true;
		}
	}
}