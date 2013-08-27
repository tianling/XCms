<?php
/**
 * @author lancelot <cja.china@gmail.com>
 * Date 2013-8-26
 * Encoding UTF-8 
 */
class AuthUser extends CWebUser{
	private $_access = array();
	/**
	 * check operation access
	 * @param array $operation. contains 'module','controller','action'
	 * @param boolean $allowCaching
	 * 
	 */
	public function checkAccess($operation,$allowCaching=true){
		if ( $allowCaching === true ){
			$operationKey = md5(json_encode($operation));
			return isset($this->_access[$operationKey]) ? $this->_access[$operationKey] : false;
		}
		
		$access = Yii::app()->getAuthManager()->checkAccess($operation,$this->getId());
		if ( $allowCaching === true ){
			$this->_access[$operationKey] = $access;
		}
	}
}