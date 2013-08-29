<?php
/**
 * @author lancelot <cja.china@gmail.com>
 * Date 2013-8-29
 * Encoding UTF-8 
 * 
 * @property AuthAssigner $instance
 * @property mixed $data
 */
class AuthAssigner extends CComponent{
	/**
	 * @var string item type used to grant or revoke
	 */
	const ITEM_USER 	= 'user';
	const ITEM_GROUP 	= 'group';
	const ITEM_ROLE 	= 'role';
	const ITEM_PERMISSION='permission';
	const ITEM_OPERATION= 'operation';
	const ITEM_RESOURCE = 'resource';
	/**
	 * @var AuthAssigner
	 */
	private static $_instance = null;
	/**
	 * @var string a grant or revoke command's subject
	 */
	private $_subject = '';
	/**
	 * @var string a grant or revoke command's predicate
	 */
	private $_predicate = '';
	/**
	 * @var string a grant or revoke command's object
	 */
	private $_object = '';
	/**
	 * @var mixed
	 */
	private $_data = null;
	/**
	 * @var array descript that a subject can be granted to or revoked from a object.
	 * array value is the associate table raw name
	 * <pre>
	 * 		self::ITEM_USER => array(
	 * 			self::ITEM_GROUP,
	 * 		)
	 * </pre>
	 * means user can be granted to group
	 */
	protected $_assignMap = array(
			self::ITEM_USER => array(
				self::ITEM_GROUP 		=> '{{user_group}}',
				self::ITEM_ROLE 		=> '{{user_role}}',
				self::ITEM_PERMISSION 	=> '{{auth_user_permission}}'
			),
			self::ITEM_GROUP => array(
				self::ITEM_USER 		=> '{{user_group}}',
				self::ITEM_ROLE 		=> '{{auth_group_role}}',
			),
			self::ITEM_ROLE => array(
				self::ITEM_USER 		=> '{{user_role}}',
				self::ITEM_GROUP 		=> '{{auth_group_role}}',
				self::ITEM_PERMISSION 	=> '{{auth_role_permission}}'
			),
			self::ITEM_PERMISSION => array(
				self::ITEM_USER 		=> '{{auth_user_permission}}',
				self::ITEM_ROLE 		=> '{{auth_role_permission}}',
				self::ITEM_OPERATION 	=> '{{auth_permission}}',
				self::ITEM_RESOURCE 	=> '{{auth_permission}}'
			),
			self::ITEM_OPERATION => array(
				self::ITEM_PERMISSION 	=> '{{auth_permission}}'
			),
			self::ITEM_RESOURCE => array(
				self::ITEM_PERMISSION 	=> '{{auth_permission}}'
			)
			
	);
	
	private function __construct(){
	}
	
	/**
	 * @return AuthAssigner
	 */
	public static function getInstance(){
		if ( self::$_instance === null ){
			self::$_instance = new AuthAssigner();
		}
		return self::$_instance;
	}
	
	/**
	 * @param string $itemType
	 * @param mixed $data
	 * @return AuthAssigner
	 */
	public function grant($itemType,$data){
		$this->_subject = $itemType;
		$this->_predicate = 'grant';
		$this->_data = $data;
		return $this;
	}
	
	/**
	 * @param string $itemType
	 * @param mixed $data
	 * @return AuthAssigner
	 */
	public function revoke($itemType,$condition){
		$this->_subject = $itemType;
		$this->_predicate = 'revoke';
		$this->_data = $condition;
		return $this;
	}
	
	/**
	 * @param string $target
	 */
	public function to($target){
		$this->_object = $target;
		return $this;
	}
	
	/**
	 * @param string $source
	 * @return AuthAssigner
	 */
	public function from($source){
		$this->_object = $source;
		return $this;
	}
	
	/**
	 * @throws CException
	 * @return boolean
	 */
	public function doit(){
		if ( empty($this->_data) ){
			return false;
		}
		$subject = $this->_subject;
		$object = $this->_object;
		if ( $this->checkConstraint($subject,$object) === false ){
			throw new CException(Yii::t('auth','s_grant_revoke_o_error'));
		}
		
		$sql = '';
		if ( $this->_predicate === 'grant' && is_array($this->_data) ){
			$table = Yii::app()->db->getSchema()->getTable($this->_assignMap[$subject][$object]);
			
			$preSql = "INSERT INTO {$table->rawName} ";
			foreach ( $this->_data as $data ){
				if ( is_array($data) ){
					$info = $this->biuldGrantSqlInfo($table,$data);
					$sql .= $preSql.'('.implode(', ',$info['fields']).') VALUES ('.implode(', ',$info['values']).');';
				}else {
					$info = $this->biuldGrantSqlInfo($table,$this->_data);
					$sql .= $preSql.'('.implode(', ',$info['fields']).') VALUES ('.implode(', ',$info['values']).');';
					break;
				}
			}
			return Yii::app()->db->createCommand($sql)->execute();
		}elseif ( $this->_predicate === 'revoke' ){
			$table = Yii::app()->db->getSchema()->getTable($this->_assignMap[$subject][$object]);
			
			$preSql = "DELETE FROM {$table->rawName} WHERE ";
			if ( is_array($this->_data) ){
				foreach ( $this->_data as $condition ){
					$sql .= $preSql.$condition;
				}
			}else {
				$sql .= $preSql.$this->_data;
			}
			return Yii::app()->db->createCommand($sql)->execute();
		}else {
			return false;
		}
	}
	
	/**
	 * @param CDbTableSchema $table
	 * @param array $data
	 * @return array[array,array]
	 */
	protected function biuldGrantSqlInfo($table,$data){
		$fields=array();
		$values=array();
		foreach($data as $name=>$value)
		{
			if(($column=$table->getColumn($name))!==null && ($value!==null || $column->allowNull))
			{
				$fields[]=$column->rawName;
				$values[]=$value;
			}
		}
		if($fields===array())
		{
			$pks=is_array($table->primaryKey) ? $table->primaryKey : array($table->primaryKey);
			foreach($pks as $pk)
			{
				$fields[]=$table->getColumn($pk)->rawName;
				$values[]=$data[$pk];
			}
		}
		return array('fields'=>$fields,'values'=>$values);
	}
	
	/**
	 * @param string $subject
	 * @param string $object
	 * @return boolean
	 */
	public function checkConstraint($subject,$object){
		return isset($this->_assignMap[$subject],$this->_assignMap[$subject][$object]);
	}
}