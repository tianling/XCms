<?php
/**
 * @name SiteSetting.php UTF-8
 * @author ChenJunAn<lancelot1215@gmail.com>
 * 
 * Date 2013-8-7
 * Encoding UTF-8
 * 
 * It is different from component configuration.
 * It is the site's setting.such as site name.
 * 
 * setting table data structure must be the same as @link core/components/environment/setting.sql
 */
class SiteSetting extends CMap{
	
	/**
	 * you can define a new table name in component configuration.
	 * @var string
	 */
	public $settingTableName = 'xcms_setting';
	/**
	 * @var string the db component id.
	 */
	public $dbComponentId = 'db';
	/**
	 * @var array internal data storage
	 */
	private $_d = array();
	/**
	 * @var boolean whether this list is read-only
	 */
	private $_r = false;
	
	/**
	 * get db connection component.
	 * @return CDbConnection
	 */
	public function getDbConnection(){
		return Yii::app()->getComponent($this->dbComponentId);
	}
	
	/**
	 * get a setting from database.
	 */
	public function itemAt($key){
		$item = parent::itemAt($key);
		
		if ( $item === null ){
			$data = $this->getIndb($key);
			if ( $data !== false ){
				$this->_d[$key] = $data;
				$item = $data;
			}
		}
		return $item;
	}
	
	/**
	 * add a setting to @property $this->_d and save into database.
	 */
	public function add($key,$value){
		if ( !$this->_r ){
			if ( $key === null ){
				throw new CException(Yii::t('yii','The key can not be null.'));
				return;
			}
			if ( parent::itemAt($key) || $this->getIndb($key) ){
				$this->update($key,$value);
			}else {
				$this->save($key,$value);
			}
			$this->_d[$key] = $value;
		}else {
			throw new CException(Yii::t('yii','The map is read only.'));
		}
	}
	
	/**
	 * delete a setting from database.
	 * @param string $key
	 * @throws CException
	 */
	public function deleteFromDb($key){
		if ( !$this->_r ){
			$this->getDbConnection()->createCommand()->delete($this->settingTableName)->where('setting_key=:key',array(':key'=>$key));
		}else {
			throw new CException(Yii::t('yii','The map is read only.'));
		}
	}
	
	/**
	 * save a key-value to database.
	 * @param string $key
	 * @param mixed $value
	 */
	protected function save($key,$value){
		if ( !$this->_r ){
			$this->getDbConnection()->createCommand()->insert($this->settingTableName,array(
					'setting_key' => $key,
					'value' => serialize($value)
			));
		}else {
			throw new CException(Yii::t('yii','The map is read only.'));
		}
	}
	
	/**
	 * update a setting value by key
	 * @param string $key
	 * @param mixed $value
	 */
	protected function update($key,$value){
		if ( !$this->_r ){
			$this->getDbConnection()->createCommand()->update($this->settingTableName,array(
					'value' => serialize($value)
			))->where('setting_key=:key',array(':key'=>$key));
		}else {
			throw new CException(Yii::t('yii','The map is read only.'));
		}
	}
	
	/**
	 * get setting data in database.
	 * @param string $key
	 * @return mixed
	 */
	protected function getIndb($key){
		$db = $this->getDbConnection();
		$data = $db->createCommand()->select('value')->from($this->settingTableName)->where('setting_key=:key',array(':key'=>$key));
		return $data === false ? false : unserialize($data['value']);
	}
}  