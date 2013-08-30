<?php
/**
 * @author lancelot <cja.china@gmail.com>
 * Date 2013-8-30
 * Encoding GBK 
 */
abstract class SingleInheritanceModel extends CmsActiveRecord{
	/**
	 * @var string parent relation defined in {@link CActiveRecord::relations()}
	 * null if this model does not have a parent
	 */
	protected $_parentRelation = null;
	/**
	 * @var CActiveRecord when parent's attribute(s) is set before get
	 * this record will be created as a new ActiveRecord
	 */
	private $_setParentBeforeGet = null;
	
	/**
	 * get parent's attribute.you can access this attribute like a property
	 * @param string $name
	 */
	public function __get($name){
		try {
			$result = parent::__get($name);
		}catch ( CException $e ){
			$parent = $this->getParentInUse();
			if ( $parent !== null ){
				$result = $parent->__get($name);
				if ( $result === null && $this->_setParentBeforeGet !== null ){
					$result = $this->getParentInUse(true)->__get($name);
				}
			}else {
				throw $e;
			}
		}
		
		return $result;
	}
	
	/**
	 * if this record has parent,set the attribute to parent record
	 * @see CActiveRecord::__set()
	 */
	public function __set($name,$value){
		$hasSetInParent = false;
		$parent = $this->getParentInUse();
		if ( $parent !== null ){
			try {
				$parent->__set($name,$value);
				$hasSetInParent = true;
			}catch ( CException $e ){
			}
		}
		if ( $hasSetInParent === false ){
			parent::__set($name,$value);
		}
	}
	
	/**
	 * @see CActiveRecord::__isset()
	 */
	public function __isset($name){
		if ( parent::__isset($name) === false ){
			$parent = $this->getParentInUse(true);
			if ( $parent !== null ){
				return $parent->__isset($name);
			}
			return false;
		}else {
			return true;
		}
	}
	
	/**
	 * @see CActiveRecord::__unset()
	 */
	public function __unset($name){
		parent::__unset($name);
		$parent = $this->getParentInUse(true);
		if ( $parent !== null ){
			$parent->__unset($name);
		}
	}
	
	/**
	 * @see CActiveRecord::attributeNames()
	 */
	public function attributeNames(){
		$selfAttributeNames = parent::attributeNames();
		$parentAttributeNames = array();
		
		$parent = $this->getParentInUse();
		if ( $parent !== null ){
			$parentAttributeNames = $parent->attributeNames();
		}
		return array_merge($selfAttributeNames,$parentAttributeNames);
	}
	
	/**
	 * @see CActiveRecord::hasAttribute()
	 */
	public function hasAttribute($name){
		if ( parent::hasAttribute($name) === false ){
			$parent = $this->getParentInUse();
			if ( $parent !== null ){
				return $parent->hasAttribute($name);
			}
		}
		return true;
	}
	
	/**
	 * @see CActiveRecord::getAttribute()
	 */
	public function getAttribute($name){
		$attribute = parent::getAttribute($name);
		if ( $attribute === null ){
			$parent = $this->getParentInUse();
			if ( $parent !== null ){
				$attribute = $parent->getAttribute($name);
				if ( $attribute === null && $this->_setParentBeforeGet !== null ){
					$attribute = $this->getParentInUse(true)->getAttribute($name);
				}
			}
		}
		return $attribute;
	}
	
	/**
	 * @see CActiveRecord::setAttribute()
	 */
	public function setAttribute($name,$value){
		$parent = $this->getParentInUse();
		if ( $parent !== null ){
				$parent->setAttribute($name,$value);
		}
		parent::setAttribute($name,$value);
	}
	
	/**
	 * @see CActiveRecord::getAttributes()
	 */
	public function getAttributes($names=true){
		$selfAttributes = parent::getAttributes($names);
		
		$parent = $this->getParentInUse(true);
		if ( $parent !== null ){
			$selfAttributes = array_merge($selfAttributes,$parent->getAttributes($names));
		}
		return $selfAttributes;
	}
	
	/**
	 * @see CModel::setAttributes()
	 */
	public function setAttributes($values,$safeOnly=true){
		parent::setAttributes($values,$safeOnly);
		$parent = $this->getParentInUse();
		if ( $parent !== null ){
			$parent->setAttributes($values,$safeOnly);
		}
	}
	
	public function insert($attributes=null){
		$parent = $this->getParentInUse();
		if ( $parent !== null && $parent->insert($attributes) ){
			$insertId = $this->getDbConnection()->getLastInsertID();
			$foreignKey = $this->getMetaData()->relations[$this->_parentRelation]->foreignKey;
			parent::setAttribute($foreignKey,$insertId);
			return parent::insert($attributes);
		}
		return false;
	}
	
	public function update($attributes=null){
		$parent = $this->getParentInUse();
		if ( $parent !== null ){
			try {
				$parent->update($attributes);
			}catch( CException $e ){
			}
		}
		parent::update($attributes);
	}
	
	/**
	 * get the using parent.this parent can be used util save
	 * @return CActiveRecord.Null if parentRelation is null
	 */
	protected function getParentInUse($useRelated=false){
		$parent = null;
		if ( $this->_parentRelation !== null ){
			if ( $this->hasRelated($this->_parentRelation) === false && $this->_setParentBeforeGet === null ){
				$relationClass = $this->getMetaData()->relations[$this->_parentRelation]->className;
				$parent = new $relationClass;
				$this->_setParentBeforeGet = $parent;
			}elseif ( !$useRelated && $this->_setParentBeforeGet !== null ){
				$parent = $this->_setParentBeforeGet;
			}elseif ( $useRelated && $this->_setParentBeforeGet !== null ){
				$parent = $this->getRelated($this->_parentRelation);
				$parent->setAttributes( array_merge($parent->getAttributes(),$this->_parentRelation->getAttributes()) );
				$this->_setParentBeforeGet = null;
			}else {
				$parent = $this->getRelated($this->_parentRelation);
			}
		}
		return $parent;
	}
}