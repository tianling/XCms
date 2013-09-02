<?php
/**
 * @name CmsSecurity.php UTF-8
 * @author ChenJunAn<lancelot1215@gmail.com>
 * 
 * Date 2013-8-8
 * Encoding UTF-8
 * 
 * 
 */
class CmsSecurity extends CSecurityManager{
	public $uuidSalt = '';
	/**
	 * generate a password
	 * @param string $inputPassword
	 * @param int $salt
	 * @return string
	 */
	public function generate($inputPassword,$cost = 15){
		return CPasswordHelper::hashPassword($inputPassword,$cost);
	}
	
	/**
	 * verify password
	 * @param string $inputPassword
	 * @param string $storedPassword
	 * @return boolean
	 */
	public function verify($inputPassword,$storedPassword){
		return CPasswordHelper::verifyPassword($inputPassword,$storedPassword);
	}
	
	/**
	 * generate a uuid
	 * @param array $rawData
	 * @return string
	 */
	public function generateUUID($rawData){
		if ( is_string($rawData) ){
			$rawData = array($rawData);
		}
		
		$string = '';
		foreach ( $rawData as $data ){
			$string .= is_string($data) ? $data : strval($data);
		}
		$string .= $this->uuidSalt === '' ? $this->generateRandomString(10) : $this->uuidSalt;
		
		$rawUuid = md5($string);
		$uuidBody = array();
		for ( $i=0; $i<31; $i+=4 ){
			$uuidBody[] = substr($rawUuid,$i,4);
		}
		shuffle($uuidBody);
		
		$uuid = $uuidBody[0].$uuidBody[1].'-';
		$uuid .= $uuidBody[2].'-'.$uuidBody[3].'-'.$uuidBody[4].'-';
		$uuid .= $uuidBody[5].$uuidBody[6].$uuidBody[7];
		
		return $uuid;
	}
}