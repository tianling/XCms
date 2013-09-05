<?php
/**
 * @name RightCalculatorAR.php UTF-8
 * @author ChenJunAn<lancelot1215@gmail.com>
 * 
 * Date 2013-8-24
 * Encoding UTF-8
 * 
 * @property RightCalculatorAR $instance
 * @property array $data
 * @property int $uid user id
 */
class RightCalculatorAR extends RightCalculatorBase{
	/**
	 * init user's roles,groups,temporary permissions via $this->getUid().
	 */
	public function initUserData(){
		$user = UserModel::model()->with(array(
				'AuthPermissions',
				'AuthGroups' => array('with'=>'AuthRole'),
				'AuthRoles' => array('with'=>'AuthPermission')
		))->findByPk($this->getUid());
		if ( $user !== null ){
			$separator = 'u'.$this->getUid();
			$userRoles = $this->initRoleWithChildren($user->AuthRoles);
			$this->storeData('userRoles',$separator,$userRoles);
			$this->storeData('userGroups',$separator,$user->AuthGroups);
			$this->storeData('userPermissions',$separator,$user->AuthPermissions);
		}
	}
	
	/**
	 * @param CActiveRecord[] $roles
	 * @return CActiveRecord[]
	 */
	private function initRoleWithChildren(&$roles){
		static $returnRole = array();
		foreach ( $roles as $role ){
			$rolePk = 'r'.$role->getPrimaryKey();
			if ( !isset($returnRole[$rolePk])){
				$returnRole[$rolePk] = $role;
			}
			
			$findCondition = "`lft`>{$role->getAttribute('lft')} AND `rgt`<{$role->getAttribute('rgt')}";
			
			$children = $role->findChildren($findCondition);
			foreach ( $children as $child ){
				$childPk = 'r'.$child->getPrimaryKey();
				if ( !isset($returnRole[$childPk])){
					$returnRole[$childPk] = $child;
				}
			}
		}
		return $returnRole;
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
				$newWithChildren = $this->initRoleWithChildren($newGroupRoles);
				$this->storeData('groupRoles',$separator,$newWithChildren);
				$groupRoles[$separator] = $newWithChildren;
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
				$rolePermissions[$separator] = $newRolePermissions;
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
		$separator = 'u'.$this->getUid();
		if ( !$refresh && isset($this->_data['finalPermissions'][$separator]) ){
			return $this->_data['finalPermissions'][$separator];
		}
		
		$userPermissions = $this->getUserPermissions();
		$rolePermissions = $this->getRolePermissions();
		$finalPermissions = array();
		
		foreach ( $rolePermissions as $rolePermission ){
			foreach ( $rolePermission as $permission ){
				$key = 'p'.$permission->getPrimaryKey();
				if ( !isset($finalPermissions[$key]) ){
					$finalPermissions[$key] = $permission;
				}
			}
		}
		
		foreach ( $userPermissions as $userPermission ){
			$key = 'p'.$userPermission->getPrimaryKey();
			if ( array_key_exists($key,$finalPermissions) ){
				if ( $userPermission->is_own == 0 || time() > $userPermission->expire ){
					$finalPermissions[$key] = null;
					unset($finalPermissions[$key]);
				}
			}else {
				if ( $userPermission->is_own == 1 || time() <= $userPermission->expire ){
					$finalPermissions[$key] = $userPermission;
				}
			}
		}
		$this->storeData('finalPermissions',$separator,$finalPermissions);
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