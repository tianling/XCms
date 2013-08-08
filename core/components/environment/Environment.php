<?php
/**
 * @name Environment.php UTF-8
 * @author ChenJunAn<lancelot1215@gmail.com>
 * 
 * Date 2013-7-22
 * Encoding UTF-8
 */
class Environment{
	const BAISC = 'ConfigBase';
	const CONSOLE_DEBUG = 'ConsoleDebug';
	const CONSOLE_SANBOX = 'ConsoleSandbox';
	const CONSOLE_READY = 'ConsoleReady';
	const CONSOLE_PRODUCTION = 'ConsoleProduction';
	const DEBUG = 'Debug';
	const SANDBOX = 'Sandbox';
	const READY = 'Ready';
	const PRODUCTION = 'Production';
	
	/**
	 * @var CApplication
	 */
	public $application;
	
	/**
	 * @var string
	 */
	protected $_applicationClass;
	
	protected $_yiiMap = array(
			'yii' => 'yii',
			'lite' => 'yiilite',
			'test' => 'yiit'
	);
	
	protected $_configMap = array(
			self::BAISC,
			self::CONSOLE_DEBUG,
			self::CONSOLE_SANBOX,
			self::CONSOLE_READY,
			self::CONSOLE_PRODUCTION,
			self::DEBUG,
			self::SANDBOX,
			self::READY,
			self::PRODUCTION
	);
	
	/**
	 * @var string
	 */
	private $_yiiType;
	
	/**
	 * @var string
	 */
	private $_configType;
	
	/**
	 * config Object
	 * @var ConfigBase
	 */
	private $_config = null;
	
	/**
	 * @param int $configType
	 */
	public function __construct($configType,$applicationClass = 'CWebApplication',$yiiType = 'yii'){
		$this->_configType = $configType;
		$this->_yiiType = $yiiType;
		$this->_applicationClass = $applicationClass;
	}
	
	/**
	 * set some varibles
	 */
	public function prepare(){
		$this->yiiInclude();
		Yii::import('cms.components.environment.*');
		$this->_setConfig();
	}
	
	/**
	 * you can do some thing before application run
	 * @return boolean
	 */
	public function beforeRun(){
		return true;
	}
	
	/**
	 * run yii application.
	 */
	public function run(){
		$this->prepare();
		defined('YII_DEBUG') or define('YII_DEBUG',$this->_config->debug);
		defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',$this->_config->traceLevel);
		$this->application = Yii::createApplication($this->_applicationClass,$this->_config->getConfig());
		
		if ( $this->beforeRun() ){
			$this->application->run();
		}
	}
	
	/**
	 * @param mixed $configType
	 */
	public function addConfigType($configType = null){
		if ( $configType === null ){
			return;
		}
		
		if ( is_string($configType) ){
			$this->_configType[] = $configType;
		}elseif ( is_array($configType) ){
			foreach ( $configType as $type ){
				$this->_configType[] = $type;
			}
		}
	}
	
	/**
	 * @return array
	 */
	public function getConfig(){
		if ( $this->_config === null ){
			$this->_setConfig();
		}
		return $this->_config;
	}
	
	/**
	 * reset config object
	 * @param string $configType
	 */
	public function resetConfig($configType = null){
		if ( $configType !== $this->_configType ){
			$this->_configType = $configType;
			$this->_setConfig();
		}
	}
	
	private function _setConfig(){
		$class = in_array($this->_configType,$this->_configMap) ? $this->_configType : self::BAISC;
		$this->_config = new $class();
		$this->_config->init();
	}
	
	/**
	 * include yii framework and set alias 'cms'
	 */
	public function yiiInclude(){
		if ( isset($this->_yiiMap[$this->_yiiType]) ){
			require $this->_yiiMap[$this->_yiiType].'.php';
			Yii::setPathOfAlias('cms',CORE_DIR);
		}else{
			throw new Exception('yii did not included.please check "YII_DIR" in cms.php.');
		}
	}
	
	
	
	
	
	
}