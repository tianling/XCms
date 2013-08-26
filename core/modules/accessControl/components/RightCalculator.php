<?php
/**
 * @name RightCalculator.php UTF-8
 * @author ChenJunAn<lancelot1215@gmail.com>
 * 
 * Date 2013-8-24
 * Encoding UTF-8
 * 
 * @property RightCalculator $instance
 * @property array $data
 * @property int $uid user id
 */
class RightCalculator extends CComponent{
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
	private $_data = array(
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
	private $_uid = null;
	
	/**
	 * @var RightCalculator
	 */
	private static $_instance = null;
	
	private function __construct(){}
	
	/**
	 * @return RightCalculator
	 */
	public static function getInstance(){
		if ( self::$_instance === null ){
			self::$_instance = new RightCalculator();
		}
		return self::$_instance;
	}
	
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
		$this->getUid() = $uid;
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
		if ( isset($this->_data[$type]) ){
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
	 * init user's roles,groups,temporary permissions via $this->getUid().
	 */
	public function initUserData(){
		$user = UserModel::model()->with('AuthPermissions','AuthGroups','AuthRoles')->findByPk($this->getUid());
		if ( $user !== null ){
			$separator = 'u'.$this->getUid();
			$this->storeData('userRoles',$separator,$user->AuthRoles);
			$this->storeData('userGroups',$separator,$user->AuthGroups);
			$this->storeData('userPermissions',$separator,$user->AuthPermissions);
		}
	}
	
	/**
	 * @param int $groupId
	 * @return array
	 */
	public function getGroupRoles($groupId=null){
		if ( $groupId !== null ){
			return $this->getStoredData('groupRoles','g'.$groupId);
		}
		$groupRoles = array();
		foreach ( $this->getUserGroups() as $userGroup ){
			$separator = 'g'.$userGroup->getPrimaryKey();
			if ( isset($this->_data['groupRoles'][$separator]) ){
				$groupRoles[$separator] = $this->_data['groupRoles'][$separator];
			}else {
				$newGroupRoles = $userGroup->AuthRole;
				$this->storeData('groupRoles',$separator,$newGroupRoles);
				$groupRoles[$separator] = $newGroupRoles;
			}
		}
		return $groupRoles;
	}
	
	/**
	 * calculate user's final roles
	 * @return array
	 */
	public function getFinalRoles($uid=null,$refresh=false){
		if ( $uid !== null ){
			return $this->getStoredData('finalRoles','u'.$uid);
		}
		if ( !$refresh && isset($this->_data['finalRoles']['u'.$this->getUid()]) ){
			return $this->_data['finalRoles']['u'.$this->getUid()];
		}
		
		$userRoles = $this->getUserRoles();
		$userGroupRoles = $this->getGroupRoles();
		
		$keys = array();
		foreach ( $userRoles as $userRole ){
			$keys[] = $userRole->getPrimaryKey();
		}
		
		foreach ( $userGroupRoles as $groupRoles ){
			foreach ( $groupRoles as $groupRole ){
				$rid = $groupRole->getPrimarykey();
				if ( !in_array($rid,$keys) ){
					$keys[] = $rid;
					$userRoles[] = $groupRole;
				}else {
					continue;
				}
			}
		}
		$this->storeData('finalRoles','u'.$this->getUid(),$userRoles);
		return $userRoles;
	}
	
	/**
	 * @param int $roleId
	 * @return array
	 */
	public function getRolePermissions($roleId=null){
		if ( $roleId !== null ){
			return $this->getStoredData('rolePermissions','r'.$roleId);
		}
		$rolePermissions = array();
		foreach ( $this->getFinalRoles() as $userRole ){
			$separator = 'r'.$userRole->getPrimaryKey();
			if ( isset($this->_data['rolePermissions'][$separator]) ){
				$rolePermissions[$separator] = $this->_data['rolePermissions'][$separator];
			}else {
				$newRolePermissions = $userRole->AuthPermission;
				$this->storeData('rolePermissions',$separator,$newRolePermissions);
				$newRolePermissions[$separator] = $newRolePermissions;
			}
		}
		return $rolePermissions;
	}
	
	/**
	 * calculate user's final permissions
	 * @return array
	 */
	public function getFinalPermissions($uid=null,$refresh=false){
		if ( $uid !== null ){
			return $this->getStoredData('finalPermissions','u'.$uid);
		}
		if ( !$refresh && isset($this->_data['finalPermissions']['u'.$this->getUid()]) ){
			return $this->_data['finalPermissions']['u'.$this->getUid()];
		}
		
		$userPermissions = $this->getUserPermissions();
		$rolePermissions = $this->getRolePermissions();
		$finalPermissions = array();
		
		foreach ( $rolePermissions as $rolePermission ){
			foreach ( $rolePermission as $permission ){
				$finalPermissions['p'.$permission->getPrimaryKey()] = $permission;
			}
		}
		
		foreach ( $userPermissions as $userPermission ){
			$separator = 'p'.$userPermission->getPrimaryKey();
			if ( array_key_exists($separator,$finalPermissions) ){
				if ( $userPermission->is_own == 0 || time() > $userPermission->expire ){
					$finalPermissions[$separator] = null;
					unset($finalPermissions[$separator]);
				}
			}else {
				if ( $userPermission->is_own == 1 || time() <= $userPermission->expire ){
					$finalPermissions[$separator] = $userPermission;
				}
			}
		}
		$this->storeData('finalPermissions','u'.$this->getUid(),$finalPermissions);
		return $finalPermissions;
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