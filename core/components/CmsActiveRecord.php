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
		if ( empty($pk) ){
			return null;
		}
		if ( ! $pk instanceof CActiveRecord ){
			return parent::findByPk($pk,$condition,$params);
		}else {
			return $pk;
		}
	}
	
	/**
	 * @return CDbTransaction
	 */
	public function getTransaction(){
		$connection = $this->getDbConnection();
		$transaction = $connection->getCurrentTransaction();
		if ( $transaction === null ){
			$transaction = $connection->beginTransaction();
		}
		
		return $transaction;
	}
}