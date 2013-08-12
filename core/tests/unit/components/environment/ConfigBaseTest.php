<?php
/**
 * @name ConfigBaseTest.php UTF-8
 * @author ChenJunAn<lancelot1215@gmail.com>
 * 
 * Date 2013-8-12
 * Encoding UTF-8
 */
class ConfigBaseTest extends CDbTestCase{
	/**
	 * @var Environment
	 */
	public $env;
	/**
	 * @var ConfigBase
	 */
	public $config;
	public $app;
	
	protected function setUp(){
		parent::setUp();
		$this->env = new Environment(new ConfigBase());
		$this->config = $this->env->getConfig();
	}
	
	public function testGetConfig(){
		$config = $this->config->getConfig();
		$this->assertInternalType('array',$config);
	}
}