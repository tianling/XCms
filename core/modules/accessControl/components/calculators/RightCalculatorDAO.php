<?php
/**
 * @author lancelot <cja.china@gmail.com>
 * Date 2013-9-5
 * Encoding GBK 
 */
class RightCalculatorDAO extends RightCalculatorBase{
	/**
	 * @var CDbConnection
	 */
	private $_db;
	
	public function init(){
		$this->_db = Yii::app()->getDb();
	}
	
	public function initUserData(){
		$command = $this->_db->createCommand();
		$where = 'u.id='.$this->getUid();
		//user permissions
		$up = $command->select('up.*')->from('xcms_user u')->join('xcms_user_permissions up','u.id=up.user_id')->where($where)->queryAll();
		$command->reset();
		$ur = $command->select('r.*')->from('xcms_user u')->join('xcms_user_group ug','u.id=ug.user_id')
		->join('xcms_auth_group_role gr','gr.group_id=ug.group_id')
		->where($where)->queryAll();
	}
	
	
	public function getGroupRoles($groupId = null) {
	}

	public function getFinalRoles($uid = null, $refresh = false) {
	}
	
	public function getRolePermissions($roleId = null) {
	}

	
	public function getFinalPermissions($uid = null, $refresh = false) {
	}

}