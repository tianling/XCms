<?php
/**
 * @name ConfigBase.php UTF-8
 * @author ChenJunAn<lancelot1215@gmail.com>
 * 
 * Date 2013-8-6
 * Encoding UTF-8
 * 
 * all config's base class
 */
class ConfigBase{
	
	/**
	 * @var boolean
	 */
	public $debug;
	
	/**
	 * @var int
	 */
	public $traceLevel;
	
	/**
	 * @var Environment
	 */
	protected $_owner;
	
	/**
	 * @var string
	 */
	protected $_basePath;
	
	/**
	 * @var array
	 */
	private $_config = array();
	
	/**
	 * @param Environment $owner
	 */
	public function __construct($owner){
		$this->owner = $owner;
		$this->_basePath = $owner->basePath;
	}
	
	public function init(){
		$this->debug = false;
		$this->traceLevel = 0;
	}
	
	public function getConfig(){
		if ( empty($this->_config) ){
			$this->_config = array_merge_recursive($this->base(),$this->merge());
			if ( $this->_basePath !== '' ){
				$this->_config['basePath'] = $this->_basePath;
			}
		}
		return $this->_config;
	}
	
	public function base(){
		return array(
			'basePath' => defined('BASE_PATH') ? BASE_PATH : dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..',
			'id' => 'Powered By XCms',
			'name' => 'XCms Application' ,
			'sourceLanguage' => 'zh_cn',
			'language' => 'zh_cn',
			
			'defaultController' => 'site',
			// Preloading 'log' component
			'preload' => array('log'),
			
			'import' => array(
				//import from application directory
				'application.models.*',
				'application.controllers.*',
				'application.components.*',
				'application.extensions.*',
		
				//import from cms
				'cms.components.*',
				'cms.components.behaviors.*',
				//'cms.components.filters.*',
				//'cms.components.pagers.*',
				'cms.extensions.*',
				'cms.models.*',
				'cms.widgets.*',
				'cms.globals.*',
			),
				
			'components'=>array(
				'user'=>array(
					'allowAutoLogin' => true,
					//'class' => 'dsdsadsa',//ddadsadsadadsada
					'guestName' => 'Guest',
				),
		
				'urlManager'=>array(
					'urlFormat'=>'path',
					'urlSuffix' => '.html',
					'showScriptName' => false,
				),
		
				//'authManager' => array(
						//dsadsadsadsadsa
				//),
					
				'passwordManager' => array(
					'class' => 'PasswordManager',
					'authKey' => '*-XCmsPasswordManager-*Enjoy! :)'
				),
		
//				'errorHandler'=>array(
// 					'errorAction'=>'error/index',
// 				),
			),
			
			
		);
	}
	
	public function merge(){
		return array();
	}
}