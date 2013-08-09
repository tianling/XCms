<?php
/**
 * @name ParamsBehavior.php UTF-8
 * @author ChenJunAn<lancelot1215@gmail.com>
 * 
 * Date 2013-8-9
 * Encoding UTF-8
 * 
 * you can traverse a property or access this property by array
 */
class ParamsBehavior extends CBehavior{
	/**
	 * @var CMap
	 */
	private $_dataMap;
	
	public function __construct($data = null,$readOnly = false){
		$this->_dataMap = new CMap($data,$readOnly);
	}
	
	public function __call($name,$parameters){
		return call_user_func_array(array($this->_dataMap,$name),$parameters);
	}
	
	public function setData($data = null){
		$this->_dataMap->copyFrom($data);
	}
	
	public function setReadOnly($readOnly){
		$this->_dataMap->readOnly = $readOnly;
	}
	
}