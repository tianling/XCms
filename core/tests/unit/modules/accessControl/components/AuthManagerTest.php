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
	/**
	 * @var CActiveRecord[]
	 */
	public static $objects=array();
	
	protected function setUp(){
		parent::setUp();
		$this->auth = Yii::app()->getAuthManager();
	}
	
	public function testCheckAccess(){
		$operation = array(
				'module' => 'access3',
				'controller' => 'access3',
				'action' => 'access3'
		);
		$r = $this->auth->checkAccess($operation,35);
	}
	
	public function testGenerate(){
		self::$objects[AuthManager::ROLE][] = $this->auth->generate(AuthManager::ROLE,array('role_name' => 'phpunit test parent role'));
		self::$objects[AuthManager::ROLE][] = $this->auth->generate(AuthManager::ROLE,array('fid'=>Yii::app()->db->lastInsertID,'role_name'=>'phpunit test child role'));
		self::$objects[AuthManager::GROUP][] = $this->auth->generate(AuthManager::GROUP,array('group_name'=>'phpunit test group'));
		self::$objects[AuthManager::OPERATION][] = $this->auth->generate(AuthManager::OPERATION,array('operation_name'=>'phpunit operation','module'=>'testModule','controller'=>'testController','action'=>'testAction'));
		self::$objects[AuthManager::RESOURCE_TYPE][] = $this->auth->generate(AuthManager::RESOURCE_TYPE,array('type_name'=>'phpunitResourceType','table_name'=>'no name'));
		self::$objects[AuthManager::RESOURCE][] = $this->auth->generate(AuthManager::RESOURCE,array('type_id'=>Yii::app()->db->lastInsertId,'resource_name'=>'phpunit test resource'));
		
		foreach ( self::$objects as $object ){
			foreach ( $object as $record ){
				$this->assertInstanceOf('CmsActiveRecord',$record);
			}
		}
	}
	
	public function testGenerateBatch(){
		$data = array(
				array('type'=>AuthManager::ROLE,'role_name'=>'phpunit test role'),
				array('type'=>AuthManager::GROUP,'group_name' => 'unit test group'),
				array('type'=>AuthManager::OPERATION,'operation_name'=>'phpunit operation','module'=>'testModule2','controller'=>'testController2','action'=>'testAction2'),
				array('type'=>AuthManager::RESOURCE_TYPE,'type_name'=>'phpunitResourceType','table_name'=>'no name')
		);
		$objects = $this->auth->generateRecords($data);
		
		foreach ( $objects as $object ){
			foreach ( $object as $record ){
				$this->assertInstanceOf('CmsActiveRecord',$record);
				$record->delete();
			}
		}
	}
	
	public function testGetItem(){
		$roleItem = $this->auth->getItem(AuthManager::ROLE,self::$objects[AuthManager::ROLE][0]->getPrimaryKey());
		$this->assertInstanceOf('CmsActiveRecord', $roleItem);
	}
	
	public function testRoleMigrate(){
		$parentRole = self::$objects[AuthManager::ROLE][0];
		$subtreeRoot = self::$objects[AuthManager::ROLE][1];
		self::$objects[AuthManager::ROLE]['testA'] = $subtreeRoot->addChild(array('role_name' => 'test a'));
		self::$objects[AuthManager::ROLE]['testB'] = $subtreeRoot->addChild(array('role_name' => 'test b'));
		self::$objects[AuthManager::ROLE]['testC'] = $subtreeRoot->addChild(array('role_name' => 'test c'));
		self::$objects[AuthManager::ROLE]['testD'] = $subtreeRoot->addChild(array('role_name' => 'test d'));
		self::$objects[AuthManager::ROLE]['testAChild'] = self::$objects[AuthManager::ROLE]['testA']->addChild(array('role_name' => 'test a child 1'));
		self::$objects[AuthManager::ROLE]['testAChild'] = self::$objects[AuthManager::ROLE]['testA']->addChild(array('role_name' => 'test a child 2'));
		
		$subtreeRoot->fid = 0;
		$subtreeRoot->save();
		
		$this->assertEquals(0,$subtreeRoot->fid);
		$this->assertEquals($subtreeRoot->id,self::$objects[AuthManager::ROLE]['testA']->fid);
		$this->assertEquals($subtreeRoot->id,self::$objects[AuthManager::ROLE]['testC']->fid);
		$this->assertEquals(self::$objects[AuthManager::ROLE]['testA']->id,self::$objects[AuthManager::ROLE]['testAChild']->fid);
		$this->assertGreaterThan($subtreeRoot->lft,self::$objects[AuthManager::ROLE]['testA']->lft);
		$this->assertLessThan(self::$objects[AuthManager::ROLE]['testA']->rgt,$subtreeRoot->rgt);
	}
	
	public function testDelete(){
		foreach ( self::$objects as $object ){
			foreach ( $object as $record ){
				$record->delete();
			}
		}
	}
	
	public function testGetAssigner(){
		$assigner = $this->auth->getAssigner();
		$this->assertInstanceOf('AuthAssigner',$assigner);
	}
	
	public function testGetCalculator(){
		$calculator = $this->auth->getCalculator();
		$this->assertInstanceOf('RightCalculator',$calculator);
	}
	
	
}