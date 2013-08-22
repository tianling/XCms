<?php
/**
 *
 * @author lancelot <cja.china@gmail.com>
 * Date 2013-8-22
 * Encoding UTF-8
 * 
 * Provide base level service.
 * Data table must contains fields `fid`,`level`,`lft`,`rgt`
 */
abstract class LevelModel extends CmsActiveRecord{
	
	private $_levelInfo = array();
	
	public function getLevelInfo(){
		return $this->_levelInfo;
	}
	
	public function setLevelInfo($info){
		$this->_levelInfo = $info;
	}
	
	public function getBoundaryPole($boundary){
		$table = $this->getMetaData()->tableSchema->getTable($this-> tableName());
		$order = $boundary. ($boundary === 'lft' ? ' ASC' : ' DESC');
		$sql = "SELECT {$boundary} FROM {$table} ORDER BY {$order} LIMIT 0,1";
		$result = $this->getDbConnection()->createCommand($sql)->queryScalar();
		
		return $result === false ? null : $result;
	}
	
	public function updateTreeOnCreate($targetNode=null){
		if ( $targetNode === null ){
			$rightPole = $this->getBoundaryPole('rgt');
			if ( $rightPole === null ){
				$rightPole = 0;
			}
			//set level info
			$this->_levelInfo = array('fid'=>0,'level'=>1,'lft'=>$rightPole+1,'rgt'=>$rightPole+2);
		}else {
			$table = $this->getMetaData()->tableSchema->getTable($this-> tableName());
			$targetRgt = $targetNode->getAttribute('rgt');
			$sql = "UPDATE {$table} SET `lft`=`lft`+2 WHERE `lft`>{$targetRgt} ;UPDATE {$table} SET `rgt`=`rgt`+2 WHERE `rgt`>={$targetRgt} ;";
			$this->getDbConnection()->createCommand($sql)->execute();
			//set level info
			$this->_levelInfo = array('fid'=>$targetNode->getAttribute('id'),
					'level'=>$targetNode->getAttribute('level')+1,
					'lft'=>$targetRgt,
					'rgt'=>$targetRgt+1
			);
		}
		return true;
	}
	
	public function updateTreeOnUpdate($subtreeRoot,$targetNode=null){
		$subtreeRoot = $this->findByPk($subtreeRoot);
		$targetNode = $this->findByPk($targetNode);
		
		if ( $targetNode === null ){
			
		}else {
			
		}
	}
	
	public function updateTreeOnDelete($subtreeRoot){
		$subtreeRoot = $this->findByPk($subtreeRoot);
		if ( $subtreeRoot === null ){
			return false;
		}
		
		$db = $this->getDbConnection();
		$table = $this->getMetaData()->tableSchema->getTable($this->tableName());
		$subtreeRootRgt = $subtreeRoot->getAttribute('rgt');
		
		$decrease = 2 * ($this->countTreeByBoundary($subtreeRoot) + 1);
		$sql = "UPDATE {$table} SET `lft`=`lft`-{$decrease} WHERE `lft`>{$subtreeRootRgt};UPDATE {$table} SET `rgt`=`rgt`-{$decrease} WHERE `rgt`>{$subtreeRootRgt};";
		$db->createCommand($sql)->execute();
		return $decrease;
	}
	
	
	
	
	
	
	
	/**
	 * Count the number of $node's children by lft and rgt.
	 * @param mixed $node
	 * @return int
	 */
	public function countTreeByBoundary($node){
		$node = $this->findByPk($node);
		if ( $node === null ){
			return false;
		}
		
		$lft = $node->getAttribute('lft');
		$rgt = $node->getAttribute('rgt');
		return $this->count("`lft`>{$lft} AND `rgt`<{$rgt}");
	}
	
	/**
	 * Count the number of $parent's children by parent id.
	 * @param mixed $parent
	 * @return int
	 */
	public function countTreeByParent($parent){
		$parent = $this->findByPk($parent);
		if ( $parent === null ){
			return false;
		}
		
		$fid = $parent->getAttribute('id');
		return $this->count("`fid`={$fid}");
	}
	
	/**
	 * Count the number in $level
	 * @param int $level
	 * @return int
	 */
	public function countTreeByLevel($level){
		if ( !is_int($level) ){
			$level = intval($level);
		}
		return $this->count("`level`={$level}");
	}
	
	/**
	 * find children whose boundary is between $node->lft and $node->rgt
	 * @param mixed $node CActiveRecord or int
	 * @param mixed $condition string or CDbCeriteria
	 * @param array $params
	 * @return array
	 */
	public function findChildrenByBoundary($node,$condition='',$params=array()){
		$node = $this->findByPk($node);
		if ( $node === null ){
			return null;
		}
		$findCondition = "`lft`>{$node->getAttribute('lft')} AND `rgt`<{$node->getAttribute('rgt')}";
		return $this->findChildren($findCondition,$condition,$params);
	}
	
	/**
	 * find direct children of $parent
	 * @param mixed $parent CActiveRecord or int
	 * @param mixed $condition string or CDbCeriteria
	 * @param array $params
	 * @return array
	 */
	public function findChildrenByParent($parent,$condition='',$params=array()){
		$parent = $this->findByPk($parent);
		if ( $parent === null ){
			return null;
		}
		$findCondition = "`fid`={$parent->getAttribute('id')}";
		return $this->findChildren($findCondition,$condition,$params);
	}
	
	/**
	 * find children whose boundary is between $node->lft and $node->rgt.And returns a preorder tree.
	 * @param mixed $node CActiveRecord or int
	 * @return array those record is order by lft.
	 */
	public function findChildrenInPreorder($node){
		$node = $this->findByPk($node);
		if ( $node === null ){
			return null;
		}
		$findCondition = "`lft`>{$node->getAttribute('lft')} AND `rgt`<{$node->getAttribute('rgt')}";
		return $this->findChildren($findCondition,array('order'=>'`lft` ASC'));
	}
	
	/**
	 * 
	 * @param string $findCondition
	 * @param mixed $criteria string or array or CDbCriteria
	 * @param array $params
	 */
	public function findChildren($findCondition,$criteria='',$params=array()){
		if ( is_array($criteria) ){
			$criteria['condition'] = $findCondition;
		}elseif ( $criteria instanceof CDbCriteria ){
			$criteria->condition = $findCondition;
		}else {
			$criteria = $findCondition;
		}
		$this->findAll($criteria,$params);
	}
}