<?php
/**
 * @author lancelot <cja.china@gmail.com>
 * Date 2013-8-26
 * Encoding UTF-8 
 */
class AuthUser extends CWebUser{
	private $_access = array();
	
	public $accessCacherId = 'SessionCache';
	
	/**
	 * check operation access
	 * @param array $operation. contains 'module','controller','action'
	 * @param array $params use to absorb params delivered in {@link CAccessControlFilter}
	 * @param boolean $allowCaching
	 * 
	 */
	public function checkAccess($operation,$params=array(),$allowCaching=true){
		if ( $allowCaching === true ){
			$operationKey = md5(json_encode($operation));
			$cachedAccess = $this->getCachedAccess($operationKey);
			if ( $cachedAccess !== null ){
				return $cachedAccess;
			}
		}
		
		$access = Yii::app()->getAuthManager()->checkAccess($operation,$this->getId()) !== false;
		if ( $allowCaching === true ){
			$this->cacheAccess($operationKey,$access);
		}
		return $access;
	}
	
	/**
	 * @param string $key
	 * @return boolean
	 */
	public function getCachedAccess($key){
		return Yii::app()->session->itemAt($key);
	}
	
	/**
	 * @param string $key
	 * @param boolean $data
	 */
	public function cacheAccess($key,$data){
		Yii::app()->session->add($key,$data);
	}
	
	public function beforeLogin($id, $states, $fromCookie){
		if ( $fromCookie === true ){
			if ( UserModel::model()->count("uuid='{$states['uuid']}'") !== 1 ){
				return false;
			}
		}
		//restore from cookie as a guest without autoRenewCookie
		if ( $fromCookie === true && $this->autoRenewCookie === false ){
			$this->renewCookie();
		}
		return true;
	}
}