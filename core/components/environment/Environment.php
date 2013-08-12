<?php
/**
 * @name Environment.php UTF-8
 * @author ChenJunAn<lancelot1215@gmail.com>
 * 
 * Date 2013-7-22
 * Encoding UTF-8
 */
class Environment{
	/**
	 * @var CApplication
	 */
	public $application;
	/**
	 * the application's base path
	 * @var string
	 */
	public $basePath = '';
	/**
	 * @var string
	 */
	protected $_applicationClass;
	/**
	 * @var array map of yii calss
	 */
	protected $_yiiMap = array(
			'yii' => 'yii',
			'lite' => 'yiilite',
			'test' => 'yiit'
	);
	/**
	 * @var string
	 */
	private $_yiiType;
	/**
	 * @var string
	 */
	private $_yiiIncludePath;
	/**
	 * @var ConfigBase
	 */
	private $_config = null;
	
	/**
	 * @param string $configObject
	 * @param string $applicationClass
	 * @param string $yiiType
	 */
	public function __construct($configObject,$applicationClass = 'CmsApplication',$yiiType = 'yii'){
		$this->setConfig($configObject);
		$this->_yiiType = $yiiType;
		$this->_applicationClass = $applicationClass;
	}
	
	/**
	 * set some varibles
	 */
	public function prepare(){
		$this->yiiInclude();
		Yii::import('cms.components.CmsApplication');
		defined('YII_DEBUG') or define('YII_DEBUG',$this->_config->debug);
		defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',$this->_config->traceLevel);
		$this->application = Yii::createApplication($this->_applicationClass,$this->_config->getConfig());
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
		if ( $this->beforeRun() ){
			$this->application->run();
		}
	}
	
	/**
	 * @return array
	 */
	public function getConfig(){
		return $this->_config;
	}
	
	/**
	 * @return string
	 */
	public function getYiiIncludePath(){
		if( empty($this->_yiiIncludePath) && defined('YII_DIR') ){
			return YII_DIR;
		}else {
			return $this->_yiiIncludePath;
		}
	}
	
	/**
	 * @param ConfigBase $configObject
	 */
	public function setConfig($configObject){
		if( $configObject instanceof ConfigBase ){
			$this->_config = $configObject;
			$this->_config->init($this);
		}else{
			throw new Exception('config object is not a instanceof ConfigBase.exit.');
		}
	}
	
	/**
	 * @param string $includePath path to yii farmework path
	 */
	public function setYiiIncludePath($includePath = null){
		$this->_yiiIncludePath = $includePath.DIRECTORY_SEPARATOR;
	}
	
	/**
	 * include yii framework and set alias 'cms'
	 */
	public function yiiInclude(){
		$yii = $this->getYiiIncludePath().$this->_yiiMap[$this->_yiiType].'.php';
		if ( isset($this->_yiiMap[$this->_yiiType]) && is_file($yii)){
			require $yii;
			Yii::setPathOfAlias('cms',CORE_DIR);
		}else{
			throw new Exception('yii did not included.please check yii include path.you can set yii include path via Environment::setYiiIncludePath()');
		}
	}
}