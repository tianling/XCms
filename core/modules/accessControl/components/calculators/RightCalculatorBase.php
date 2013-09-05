<?php
/**
 * @author lancelot <cja.china@gmail.com>
 * Date 2013-9-5
 * Encoding GBK 
 */
abstract class RightCalculatorBase extends CComponent{
	/**
	 * stores calculated data
	 * 'userGroups'.Data of users' groups.Separated by user id.
	 * 'userRoles'.Data of user roles.Separated by user id.
	 * 'groupRoles'.Data of groups' roles.Separated by group id.
	 * 'finalRoles'.Final data of users' roles.Separated by user id.
	 * finalRoles = userRoles + groupRoles.
	 * 'userPermissions'.Data of users' temporary permissions.Separated by user id.
	 * 'rolePermissions'.Data of roles' permissions.Separated by role id.
	 * 'finalPermissions'.Final data of roles' permissions.Separated by role id.
	 * finalPermissions = userPermissions + rolePermissions.
	 * @var array
	 */
	protected $_data = array(
			'userGroups' 		=> null,
			'userRoles' 		=> null,
			'groupRoles' 		=> null,
			'finalRoles' 		=> null,
			'userPermissions' 	=> null,
			'rolePermissions' 	=> null,
			'finalPermissions' 	=> null,
	);
	
	/**
	 * user id
	 * @var int
	*/
	protected $_uid = null;
	
	public function init(){
		
	}
	
	/**
	 * init user's roles,groups,temporary permissions via $this->getUid().
	 */
	abstract public function initUserData();
	
	/**
	 * @param int $groupId
	 * @return array
	 */
	abstract public function getGroupRoles($groupId=null);
	
	/**
	 * calculate user's final roles
	 * @return array
	 */
	abstract public function getFinalRoles($uid=null,$refresh=false);
	
	/**
	 * @param int $roleId
	 * @return array
	 */
	abstract public function getRolePermissions($roleId=null);
	
	/**
	 * calculate user's final permissions
	 * @return array
	 */
	abstract public function getFinalPermissions($uid=null,$refresh=false);
	
	
	
	/**
	 * @param string $attr the name of attribute that will be reset.
	 * @param string $separator
	 * reset all data if attr is null.
	 * @throws CException
	 */
	public function reset($type=null,$separator=null){
		if ( $type === null ){
			foreach ( $this->_data as $key => $value ){
				$this->_data[$key] = null;
			}
		}elseif ( isset($this->_data[$type] )) {
			if ( $separator !== null && isset($this->_data[$type][$separator]) ){
				$this->_data[$type][$separator] = null;
			}else {
				$this->_data[$type] = null;
			}
		}else {
			throw new CException(Yii::t('auth','calculator_reset_wrong'));
		}
	}
	
	/**
	 * @param int $uid
	 * @throws CException
	 */
	public function setUid($uid){
		if ( empty($uid) || !is_int($uid) ){
			throw new CException(Yii::t('auth','calculator_uid_set_wrong'));
		}
		$this->_uid = $uid;
	}
	
	/**
	 * @throws CException
	 * @return int
	 */
	public function getUid(){
		if ( empty($this->_uid) || !is_int($this->_uid) ){
			throw new CException(Yii::t('auth','calculator_uid_get_wrong'));
		}
		return $this->_uid;
	}
	
	/**
	 * get $this->_data by data type and separator
	 * @param string $type
	 * @param mixed $separator
	 * @return array
	 */
	protected function getStoredData($type,$separator){
		return isset($this->_data[$type][$separator]) ? $this->_data[$type][$separator] : array();
	}
	
	/**
	 * store data in $this->_data by type and separator.
	 * data will be reset if type and separator exists.
	 * @param string $type
	 * @param mixed $separator
	 * @param mixed $data
	 */
	protected function storeData($type,$separator,&$data){
		if ( array_key_exists($type,$this->_data) ){
			if ( isset($this->_data[$type][$separator]) ){
				$this->_data[$type][$separator] = null;
				$this->_data[$type][$separator] = $data;
			}else {
				$this->_data[$type][$separator] = $data;
			}
		}
	}
	
	/**
	 * @return array
	 */
	public function getUserRoles(){
		if ( $this->_data['userRoles'] === null ){
			$this->initUserData();
		}
		return $this->getStoredData('userRoles','u'.$this->getUid());
	}
	
	/**
	 * @return array
	 */
	public function getUserGroups(){
		if ( $this->_data['userGroups'] === null ){
			$this->initUserData();
		}
		return $this->getStoredData('userGroups','u'.$this->getUid());
	}
	
	/**
	 * @return array
	 */
	public function getUserPermissions(){
		if ( $this->_data['userPermissions'] === null ){
			$this->initUserData();
		}
		return $this->getStoredData('userPermissions','u'.$this->getUid());
	}
	
	/**
	 * run calculator to get final permissions
	 * @param int $uid
	 * @return array
	 */
	public function run($uid=null){
		if ( $uid !== null ){
			$this->setUid($uid);
		}
		return $this->getFinalPermissions();
	}
}