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
		$roleData = array('role_name' => 'phpunit test parent role');
		$object = $this->auth->generate(AuthManager::ROLE,$roleData);
		
		$data = array(
				array('type'=>AuthManager::ROLE,'fid'=>Yii::app()->db->lastInsertId,'role_name'=>'phpunit test child role'),
				array('type'=>AuthManager::GROUP,'group_name'=>'phpunit test group'),
				array('type'=>AuthManager::OPERATION,'operation_name'=>'phpunit operation','module'=>'testModule','controller'=>'testController','action'=>'testAction'),
				//array('type'=>AuthManager::PERMISSION,'operation_id'=>Yii::app()->db->lastInsertId,'permission_name'=>'phpunitPermission'),
				array('type'=>AuthManager::RESOURCE_TYPE,'type_name'=>'phpunitResourceType','table_name'=>'no name'),
				//array('type'=>AuthManager::RESOURCE,'type_id'=>Yii::app()->db->lastInsertId,'resource_name'=>'phpunit test resource'),
		);
		$this->assertInstanceOf('CmsActiveRecord',$object);
		$object->delete();
		
		$objects = $this->auth->generateRecords($data);
		$this->assertInternalType('array',$objects);
		
		foreach ( $objects as $object ){
			foreach ( $object as $record ){
				$this->assertInstanceOf('CmsActiveRecord',$record);
				$record->delete();
			}
		}
	}
	
	public function testUpdateLevelModel(){
 		$role = AuthRoles::model()->findByPk(2);
 		$role->fid = 0;
	  	$role->save();
		$r = $this->auth->checkAccess(array('module'=>'','controller'=>'','action'=>''),1);
		var_dump($r);
	}
}