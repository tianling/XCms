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
}