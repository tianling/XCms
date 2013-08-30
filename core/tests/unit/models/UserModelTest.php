<?php
/**
 * @author lancelot <cja.china@gmail.com>
 * Date 2013-8-30
 * Encoding GBK 
 */
class UserModelTest extends CDbTestCase{
	protected function setUp(){
		parent::setUp();
	}
	
	public function testSetAttribute(){
		$user = new Administrators();
		$user->nickname = 'nickname';//base user
		//$user->surname = 'surname';//admin
		echo $user->nickname;
	}
}