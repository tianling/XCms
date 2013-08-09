<?php
/**
 * @name PasswordManager.php UTF-8
 * @author ChenJunAn<lancelot1215@gmail.com>
 * 
 * Date 2013-8-8
 * Encoding UTF-8
 * 
 * 
 */
class PasswordManager extends CApplicationComponent{
	const TYPE_CMS = 'cms';
	const TYPE_YII = 'yii';
	const TYPE_AUTO = 'auto';
	
	public $useType = self::TYPE_AUTO;
	
	public function init(){
		$this->decideType();
	}
	
	public function setUseType($useType = self::TYPE_AUTO){
		$this->useType = $useType;
		$this->decideType();
	}
	
	/**
	 * decide which method will be used.
	 */
	public function decideType(){
		if ( $this->useType !== self::TYPE_CMS && $this->chechBlowfish() ){
			$this->useType = self::TYPE_YII;
		}
	}
	
	/**
	 * check if blowfish is supported
	 * @return boolean
	 */
	public function chechBlowfish(){
		return function_exists('crypt') && defined('CRYPT_BLOWFISH') && CRYPT_BLOWFISH;
	}
	
	/**
	 * generate a password
	 * @param string $inputPassword
	 * @param int $salt
	 * @return string
	 */
	public function generate($inputPassword,$cost = 15){
		return $this->_dispatch(__FUNCTION__,array($inputPassword,$cost));
	}
	
	/**
	 * verify password
	 * @param string $inputPassword
	 * @param string $storedPassword
	 * @param string $salt
	 * @return boolean
	 */
	public function verify($inputPassword,$storedPassword,$salt = null){
		return $this->_dispatch(__FUNCTION__,array($inputPassword,$storedPassword,$salt));
	}
	
	/**
	 * @param string $inputPassword
	 * @param int $cost
	 * @return string
	 */
	public function cms_generate($inputPassword,$cost = 15){
		$salt = $this->cms_generateRandomString($cost);
		$password = $this->cms_encrypt($inputPassword,$salt);
		return array('salt'=>$salt,'password'=>$password);
	}
	
	/**
	 * @param string $inputPassword
	 * @param string $storedPassword
	 * @param string $salt
	 * @return boolean
	 */
	public function cms_verify($inputPassword,$storedPassword,$salt = ''){
		return $storedPassword === $this->cms_encrypt($inputPassword,$salt);
	}
	
	/**
	 * generate a random string.
	 * @param int $len
	 * @return string
	 */
	protected function cms_generateRandomString($len = 15){
		$randStr = '';
		for ( $i = 0; $i < $len; ++$i ){
			$randStr .= chr(mt_rand(33,126));
		}
		return $randStr;
	}
	
	/**
	 * return a password string
	 * @param string $txt
	 * @param string $key
	 * @return string
	 */
	protected function cms_encrypt($txt, $key = '') {
		$key	= $key ? $key : $this->_key;

		$len	= strlen($key);
		$code	= null;
		$k		= null;
		for($i=0; $i<strlen($txt); ++$i){
			$k		= $i % $len;
			$code  .= $txt[$i] ^ $key[$k];
		}
		$password = md5($code.$key);
	
		return $password;
	}
	
	public function yii_generate($inputPassword,$cost = 15){
		return array(
				'password' => CPasswordHelper::hashPassword($inputPassword,$cost)
		);
	}
	
	public function yii_verify($inputPassword,$storedPassword){
		return CPasswordHelper::verifyPassword($inputPassword,$storedPassword);
	}
	
	/**
	 * dispatch password management request to internal method.
	 * @param string $method
	 * @param array $params
	 * @throws ParamException
	 * @return mixed
	 */
	protected function _dispatch($method,$params = array()){
		$internalMethod = $this->useType.'_'.$method;
		if ( method_exists($this,$internalMethod) ){
			return call_user_func_array(array($this,$internalMethod),$params);
		}else {
			$message = __CLASS__.'::'.$internalMethod.'() is not exists.';
			Yii::trace('ParamException in PasswordManager.'.$message);
			throw new ParamException(Yii::t('cms',$message));
		}
	}
	
	
	
}