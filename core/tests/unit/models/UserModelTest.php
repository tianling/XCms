<?php
/**
 * @author lancelot <cja.china@gmail.com>
 * Date 2013-8-30
 * Encoding GBK 
 */
class UserModelTest extends CDbTestCase{
	public $testData = array();
	public static $insertId;
	protected function setUp(){
		parent::setUp();
		$this->testData = array(
				'nickname' => 'testNick',
				'realname' => 'real',
				'email' => 'testEmail',
				'password' => 'testPassword',
				'salt' => 'testSalt',
				'last_login_time' => time(),
				'last_login_ip' => '127.0.0.1',
				'locked' => 1,
				'surname' => 'surname',
				'name' => 'testName'
		);
	}
	
	public function testSetAttribute(){
		$user = new Administrators();
		foreach ( $this->testData as $key => $value ){
			$user->$key = $value;
			$getValue = $user->$key;
			$this->assertEquals($getValue,$value);
		}
	}
	
	public function testSetAttributes(){
		$user = new Administrators();
		$user->attributes = $this->testData;
		foreach ( $user->attributes as $name => $attribute ){
			$this->assertEquals($this->testData[$name],$attribute);
		}
	}
	
	public function testInsert(){
		$user = new Administrators('create');
		$user->attributes = $this->testData;
		$result = $user->save();
		
		$this->assertTrue($result);
		self::$insertId = $user->id;
	}
	
	public function testUpdate(){
		$user = Administrators::model()->with('BaseUser')->findByPk(self::$insertId);
		$this->assertInstanceOf('SingleInheritanceModel',$user);
		
		$user->nickname = 'new nick';
		$result = $user->save();
		$this->assertTrue($result);
		
		$user->surname = 'new sur';
		$result = $user->save();
		$this->assertTrue($result);
		
		$user->nickname = 'new nick2';
		$user->surname = 'new sur2';
		$result = $user->save();
		$this->assertTrue($result);
	}
	
	public function testAttributeNames(){
		$user = Administrators::model()->findByPk(self::$insertId);
		
		$names = $user->attributeNames();
		
		$this->assertContains('nickname',$names);
		$this->assertContains('surname',$names);
	}
	
	public function testGetAttribute(){
		$user = Administrators::model()->findByPk(self::$insertId);
		$nickname = $user->getAttribute('nickname');
		$this->assertInternalType('string',$nickname);
		$surname = $user->getAttribute('surname');
		$this->assertInternalType('string',$surname);
		
		$user = null;
		$user = Administrators::model()->with('BaseUser')->findByPk(self::$insertId);
		$nickname = $user->getAttribute('nickname');
		$this->assertInternalType('string',$nickname);
		$surname = $user->getAttribute('surname');
		$this->assertInternalType('string',$surname);
	}
	
	public function testHasAttribute(){
		$user = Administrators::model()->with('BaseUser')->findByPk(self::$insertId);
		$has = $user->hasAttribute('nickname');
		$this->assertTrue($has);
		$has = $user->hasAttribute('surname');
		$this->assertTrue($has);
		
		$user = null;
		
		$user = Administrators::model()->findByPk(self::$insertId);
		$has = $user->hasAttribute('nickname');
		$this->assertTrue($has);
		$has = $user->hasAttribute('surname');
		$this->assertTrue($has);
	}
	
	public function testGetErrors(){
		$user = new Administrators();
		
		$errorData = $this->testData;
		$errorData['realname'] = 'longrealname';
		$errorData['surname'] = 'verylongsurname';
		
		$user->attributes = $errorData;
		
		$isFalse = $user->validate();
		$this->assertFalse($isFalse);
		
		$realNameError = $user->getError('realname');
		$this->assertInternalType('string',$realNameError);
		
		$errors = $user->getErrors();
		$this->assertInternalType('array',$errors);
		
		foreach ( $errors as $error ){
			$this->assertInternalType('array',$error);
			
		}
	}
	
	public function testDelteRecord(){
		$effectRow = UserModel::model()->deleteByPk(self::$insertId);
		
		$this->assertEquals(1,$effectRow);
	}
}