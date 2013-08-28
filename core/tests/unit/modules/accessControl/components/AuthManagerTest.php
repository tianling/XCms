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
	
	public function testGenerateRole(){
		//$data = array('fid' => 0,'role_name' => 'test role4');
		//$this->auth->generate(AuthManager::ROLE,$data);
	}
	
	public function testGenerateRecordsRole(){
		$data = array(
				/*
				array('fid' => 0,'role_name' => 'root'),
  				array('fid' => 1,'role_name' => 'a'),
 				array('fid' => 2,'role_name' => 'b'),
 				array('fid' => 2,'role_name' => 'c'),
				array('fid' => 2,'role_name' => 'd'),
				*/
				/*
				array('fid'=>4,'role_name'=>'e'),
				array('fid'=>4,'role_name'=>'f'),
				array('fid'=>5,'role_name'=>'g'),
				*/
				/*
				array('fid'=>6,'role_name'=>'h'),
				array('fid'=>6,'role_name'=>'i'),
				*/
		);
		//$r = $this->auth->generateRecords($data,AuthManager::ROLE);
	}
	
	public function testUpdateLevelModel(){
		$role = AuthRoles::model()->findByPk(2);
 		$role->fid = 0;
 		$role->save();
	}
}