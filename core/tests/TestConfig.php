<?php
/**
 * @name TestConfig.php UTF-8
 * @author ChenJunAn<lancelot1215@gmail.com>
 * 
 * Date 2013-8-12
 * Encoding UTF-8
 */
class TestConfig extends ConfigBase{
	public function merge(){
		return array(
				'modules' => array(
						'gii'=>array(
								'class'=>'system.gii.GiiModule',
								'password'=>'lancelot!410',
								'ipFilters'=>array('127.0.0.1','::1'),
						),
				),
				'components' => array(
						'log'=>array(
								'class'=>'CLogRouter',
								'routes'=>array(
										array(
												'class'=>'CWebLogRoute',
												'levels'=>'error, warning',
										),
								),
						),
						'db' => array(
								'class' => 'system.db.CDbConnection',
								'tablePrefix' => 'xcms_',
								'autoConnect' => false,
								'connectionString' => 'mysql:host=www.caixiao2.com;dbname=shequbao',
								'emulatePrepare' => true,
								'username' => 'sqb-sandbox',
								'password' => 'sqb-sandbox@caixiao2',
								'charset' => 'utf8',
						),
				),
		);
	}
}