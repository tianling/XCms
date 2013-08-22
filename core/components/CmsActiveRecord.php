<?php
/**
 * @name CmsActiveRecord.php UTF-8
 * @author ChenJunAn<lancelot1215@gmail.com>
 * 
 * Date 2013-8-8
 * Encoding UTF-8
 */
class CmsActiveRecord extends CActiveRecord{
	public function findByPk($pk,$condition='',$params=array()){
		if ( $pk === null ){
			return null;
		}
		if ( ! $pk instanceof CActiveRecord ){
			return $this->findByPk($pk,$condition,$params);
		}else {
			return $pk;
		}
	}
}