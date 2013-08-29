<?php
/**
 * @author lancelot <cja.china@gmail.com>
 * Date 2013-8-27
 * Encoding GBK 
 */
class AuthManagerTest extends CDbTestCase{
	/**
	 * @var AuthManager
	 */
	public $auth;
	
	protected function setUp(){
		parent::setUp();
		$this->auth = Yii::app()->getAuthManager();
	}
	
	public function testGenerate(){
		$data = array(
				array('role_name' => 'root'),
		);
		$data = array('role_name' => 'test',
				'testColumn' => 'dasdsa'
		);
		$object = $this->auth->generate(AuthManager::ROLE, $data);
		$object->delete();
		//$r = $this->auth->generateRecords($data,AuthManager::ROLE);
	}
	
	public function testUpdateLevelModel(){
// 		$role = AuthRoles::model()->findByPk(2);
// 		$role->fid = 0;
//  		$role->save();
		//$r = $this->auth->checkAccess(array('module'=>'','controller'=>'','action'=>''),1);
		//var_dump($r);
	}
}