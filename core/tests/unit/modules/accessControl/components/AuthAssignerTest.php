<?php
/**
 * @author lancelot <cja.china@gmail.com>
 * Date 2013-9-4
 * Encoding GBK 
 */
class AuthAssignerTest extends CDbTestCase{
	/**
	 * @var AuthAssigner
	 */
	public $assigner;
	/**
	 * @var UserModel
	 */
	public static $user;
	/**
	 * @var CActiveRecord[]
	 */
	public static $items=array();
	
	protected function setUp(){
		parent::setUp();
		$this->assigner = Yii::app()->getAuthManager()->getAssigner();
	}
	
	public function testGerate(){
		self::$user = new UserModel();
		self::$user->attributes = array(
				'nickname' => 'AuthAssignerTest',
				'realname' => 'xixi',
				'password' => 'phpunit test AuthAssignerTest user',
		);
		if ( !self::$user->save() ){
			throw new Exception('can not create user');
		}
		$itemData = array(
				array('type'=>AuthManager::GROUP,'group_name'=>'assigner group'),
				array('type'=>AuthManager::ROLE,'role_name'=>'assigner role'),
				array('type'=>AuthManager::OPERATION,'operation_name'=>'assigner operation','module'=>'testModule','controller'=>'testController','action'=>'testAction'),
				array('type'=>AuthManager::RESOURCE_TYPE,'type_name'=>'assignerResourceType','table_name'=>'no name')
		);
		self::$items = Yii::app()->getAuthManager()->generateRecords($itemData);
		self::$items[AuthManager::RESOURCE][] = Yii::app()->getAuthManager()->generate(AuthManager::RESOURCE,array('type_id'=>Yii::app()->db->lastInsertId,'resource_name'=>'phpunit test resource'));
		return true;
	}
	
	public function testGrant(){
		$data = array(
				AuthAssigner::ITEM_GROUP => array(
						AuthAssigner::ITEM_USER => array('user_id'=>self::$user->getPrimaryKey(),'group_id'=>self::$items[AuthManager::GROUP][0]->getPrimaryKey()),
				),
				AuthAssigner::ITEM_ROLE => array(
						AuthAssigner::ITEM_USER => array('user_id'=>self::$user->getPrimaryKey(),'role_id'=>self::$items[AuthManager::ROLE][0]->getPrimaryKey()),
						AuthAssigner::ITEM_GROUP => array('role_id'=>self::$items[AuthManager::ROLE][0]->getPrimaryKey(),'group_id'=>self::$items[AuthManager::GROUP][0]->getPrimaryKey()),
				),
// 				AuthAssigner::ITEM_PERMISSION => array(
// 						AuthAssigner::ITEM_ROLE => array('role_id'=>self::$items[AuthManager::ROLE][0]->getPrimaryKey(),'permission_id'=>self::$items[AuthManager::PERMISSION][0]->getPrimaryKey()),
// 						AuthAssigner::ITEM_USER => array('permission_id'=>self::$items[AuthManager::PERMISSION][0]->getPrimaryKey(),'user_id'=>self::$user->getPrimaryKey(),'is_own'=>1,'expire'=>time()+3600*24)
// 				),
				AuthAssigner::ITEM_OPERATION => array(
						AuthAssigner::ITEM_PERMISSION 	=> array('operation_id'=>self::$items[AuthManager::OPERATION][0]->getPrimaryKey(),'permission_name'=>'assigner perimission')
				),
				AuthAssigner::ITEM_RESOURCE => array(
						AuthAssigner::ITEM_PERMISSION 	=> array('operation_id'=>self::$items[AuthManager::OPERATION][0]->getPrimaryKey(),
								'resource_id'=>self::$items[AuthManager::RESOURCE][0]->getPrimaryKey(),
								'permission_name'=>'assignerPerimission2'
						)
				),
		);
		
		foreach ( $data as $key => $item ){
			foreach ( $item as $name => $d ){
				$result = $this->assigner->grant($key,$d)->to($name)->doit();
				$this->assertEquals(1,$result);
			}
		}
		
		$permission = Yii::app()->db->createCommand("select id from xcms_auth_permission where resource_id=".self::$items[AuthManager::RESOURCE][0]->getPrimaryKey(),' AND operation_id='.self::$items[AuthManager::OPERATION][0]->getPrimaryKey())->queryAll();
		$data = null;
		$data = array(
				AuthAssigner::ITEM_PERMISSION => array(
						AuthAssigner::ITEM_ROLE => array('role_id'=>self::$items[AuthManager::ROLE][0]->getPrimaryKey(),'permission_id'=>$permission[0]['id']),
				 		AuthAssigner::ITEM_USER => array('permission_id'=>$permission[0]['id'],'user_id'=>self::$user->getPrimaryKey(),'is_own'=>1,'expire'=>time()+3600*24)
 				),
		);
		
		foreach ( $data as $key => $item ){
			foreach ( $item as $name => $d ){
				$result = $this->assigner->grant($key,$d)->to($name)->doit();
				$this->assertEquals(1,$result);
			}
		}
	}
	
	public function testClear(){
		self::$user->delete();
		foreach ( self::$items as $object ){
			foreach ( $object as $record ){
				$record->delete();
			}
		}
	}
	
}