<?php
/**
 * @name Debug.php UTF-8
 * @author ChenJunAn<lancelot1215@gmail.com>
 * 
 * Date 2013-8-6
 * Encoding UTF-8
 */
class Debug extends ConfigBase{
	
	public function init(){
		$this->debug = true;
		$this->traceLevel = 3;
	}
	
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
				),
				'params' => array(
						'test' => 'dasdsad'
				),
		);
	}
}