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
	/**
	 * @var array
	 */
	private $_levelInfo = array();
	
	public function insert($attributes=null){
		$this->updateTreeOnCreate($this->getAttribute('fid'));
		$this->setAttributes($this->_levelInfo);
		
		return parent::insert($attributes);
	}
	
	public function updateByPk($pk,$attributes,$condition='',$params=array()){
		$fid = isset($attributes['fid']) ? $attributes['fid'] : '';
		$old = $this->findByPk($pk);
		if ( $old === null ){
			return false;
		}elseif ( $old->getAttribute('fid') != $fid ) {
			$transaction = $this->getTransaction();
			
			try {
				parent::updateByPk($pk,$attributes,$condition,$params);
				if ( $this->updateTreeOnMigrate($old,$fid) ){
					$transaction->commit();
					return true;
				}else {
					$transaction->rollback();
					return false;
				}
			}catch ( CException $e ){
				$transaction->rollback();
				return false;
			}
			
		}else {
			return parent::updateByPk($pk,$attributes,$condition,$params);
		}
	}
	
	/**
	 * delete a record and delete its' children
	 */
	public function deleteByPk($pk,$condition='',$params=array()){
		$record = $this->findByPk($pk);
		if ( $record === null ){
			return false;
		}else {
			$transaction = $this->getTransaction();
			
			try {
				$updateTreeCommand = $this->updateTreeOnDelete($record,true);
				$effctRow = $this->deleteChildren($record);
				$updateTreeCommand->execute();
				$effctRow += parent::deleteByPk($pk,$condition,$params);
				if ( $effctRow > 0 ){
					$transaction->commit();
				}else {
					$transaction->rollback();
				}
				return $effctRow;
			}catch ( CException $e ){
				$transaction->rollback();
				return false;
			}
		}
	}
	
	/**
	 * delete the children of a node.
	 * @param CActiveRecord $node
	 * @return boolean
	 */
	public function deleteChildren($node=null){
		$node = $this->findByPk($node);
		if ( $node === null ){
			return false;
		}
		return $this->deleteAll("`lft`>{$node->lft} AND `rgt`<{$node->rgt}");
	}
	
	/**
	 * @return array
	 */
	public function getLevelInfo(){
		return $this->_levelInfo;
	}
	
	/**
	 * 
	 * @param array $info
	 */
	public function setLevelInfo($info){
		$this->_levelInfo = $info;
	}
	
	/**
	 * get a boundary's pole value
	 * @param string $boundary
	 * @return int.return NULL if result not found. 
	 */
	public function getBoundaryPole($boundaryName){
		$table = $this->getMetaData()->tableSchema->rawName;
		$order = $boundaryName. ($boundaryName === 'lft' ? ' ASC' : ' DESC');
		$sql = "SELECT `{$boundaryName}` FROM {$table} ORDER BY {$order} LIMIT 0,1";
		$result = $this->getDbConnection()->createCommand($sql)->queryScalar();
		
		return $result === false ? null : $result;
	}
	
	/**
	 * update preorder tree before insert
	 * @param CAcvtiveRecord $targetNode
	 * @param boolean $returnCommand
	 * @return boolean|CDbCommand
	 */
	public function updateTreeOnCreate($targetNode=null,$returnCommand=false){
		$targetNode = $this->findByPk($targetNode);
		if ( $targetNode === null ){
			$rightPole = $this->getBoundaryPole('rgt');
			if ( $rightPole === null ){
				$rightPole = 0;
			}
			//set level info
			$this->_levelInfo = array('fid'=>0,'level'=>1,'lft'=>$rightPole+1,'rgt'=>$rightPole+2);
		}else {
			$table = $this->getMetaData()->tableSchema->rawName;
			$targetRgt = $targetNode->getAttribute('rgt');
			$sql = "UPDATE {$table} SET `lft`=`lft`+2 WHERE `lft`>{$targetRgt};UPDATE {$table} SET `rgt`=`rgt`+2 WHERE `rgt`>={$targetRgt};";
			$command = $this->getDbConnection()->createCommand($sql);
			//set level info
			$this->_levelInfo = array('fid'=>$targetNode->getPrimaryKey(),
					'level'=>$targetNode->getAttribute('level')+1,
					'lft'=>$targetRgt,
					'rgt'=>$targetRgt+1
			);
			
			if ( $returnCommand === true ){
				return $command;
			}else {
				$command->execute();
			}
			
		}
		return true;
	}
	
	/**
	 * update preorder tree before migrate.
	 * 1.find all children and order by lft
	 * 2.delete subtree simulatly(this step won't delete any record)
	 * 3.create subtree simulatly(this step won't create any record).And get level info of each creation
	 * 4.update subtree level info(move node)
	 * @param CActiveRecord $subtreeRoot
	 * @param CActiveRecord $targetNode
	 * @return boolean
	 */
	public function updateTreeOnMigrate($subtreeRoot,$targetNode=null){
		$subtreeRoot = $this->findByPk($subtreeRoot);
		
		if ( $subtreeRoot !== null ){
			$refreshInfo = array();
			$targetNode = $this->findByPk($targetNode);
			
			$preorderTree = $this->findChildrenInPreorder($subtreeRoot);
			
			$this->updateTreeOnDelete($subtreeRoot);
			if ( $targetNode !== null ){//refresh target level info after virtual delete
				$targetNode->refresh();
			}
			
			$updateOnCreateommand = array();
			foreach ( $preorderTree as $preorderTreeNode ){
				$parentKey = $preorderTreeNode['parent'];
				$nodePk = $preorderTreeNode['record']->getPrimaryKey();
				if ( $parentKey !== null ){
					$targetNode = $preorderTree[$parentKey]['record'];
				}
				
				$updateOnCreateommand[] = $this->updateTreeOnCreate($targetNode,true);
				if ( $parentKey !== null ){
					$lft = $this->_levelInfo['lft'];
					$rgt = $this->_levelInfo['rgt'];
					$tmpParentKey = $parentKey;
					$preorderTreeNode['record']->setAttribute('rgt',$rgt);
					while ( $tmpParentKey !== null ){
						++$rgt;
						$preorderTree[$tmpParentKey]['record']->setAttributes(array(
								'rgt' => $rgt
						));
						$refreshInfo[$tmpParentKey]['data']['rgt'] = $rgt;
						$tmpParentKey = $preorderTree[$tmpParentKey]['parent'];
					}
					
				}else {
					$preorderTreeNode['record']->setAttributes($this->_levelInfo);
				}
				
				$refreshInfo['n'.$nodePk] = array('pk'=>$nodePk,'data'=>$this->_levelInfo);
			}
			
			$updateOnCreateSql = '';
			foreach ( $updateOnCreateommand as $command ){
				if ( is_object($command) ){
					$updateOnCreateSql .= $command->getText();
				}
			}
			
			$updateSql = '';
			$table = $this->getMetaData()->tableSchema;
			foreach ( $refreshInfo as $count => $info ){
				$condition = "`{$table->primaryKey}`={$info['pk']}";
				$data = $info['data'];
				$updateSql .= "UPDATE {$table->rawName} SET ";
				$updateSql .= "`fid`={$data['fid']},`level`={$data['level']},`lft`={$data['lft']},`rgt`={$data['rgt']} ";
				$updateSql .= "WHERE {$condition};";
				$data = null;
			}
			
			if ( !is_object($command) ){
				$command = $this->getDbConnection()->createCommand();
			}
			$command->reset()->setText($updateOnCreateSql)->execute();
			$command->reset()->setText($updateSql)->execute();
			return true;
		}else {
			return false;
		}
	}
	
	/**
	 * update preorder tree before delete
	 * @param CActiveRecord $subtreeRoot
	 * @param boolean $returnCommand return update command if true
	 * @param $disableChildren update children's lft and rgt to an impossible value
	 * @return mixed
	 */
	public function updateTreeOnDelete($subtreeRoot,$returnCommand=false,$disableChildren=true){
		$subtreeRoot = $this->findByPk($subtreeRoot);
		if ( $subtreeRoot === null ){
			return false;
		}
		
		$table = $this->getMetaData()->tableSchema->rawName;
		$subtreeRootLft = $subtreeRoot->getAttribute('lft');
		$subtreeRootRgt = $subtreeRoot->getAttribute('rgt');
		
		$decrease = 2 * ($this->countTreeByBoundary($subtreeRoot) + 1);
		$sql = "UPDATE {$table} SET `lft`=`lft`-{$decrease} WHERE `lft`>{$subtreeRootRgt};";
		$sql .= "UPDATE {$table} SET `rgt`=`rgt`-{$decrease} WHERE `rgt`>={$subtreeRootRgt};";
		if ( $disableChildren === true ){
			$sql .= "UPDATE {$table} SET `lft`=-1,`rgt`=-1 WHERE `lft`>{$subtreeRootLft} AND `rgt`<{$subtreeRootRgt};";
		}
		$command = $this->getDbConnection()->createCommand($sql);
		if ( $returnCommand === true ){
			return $command;
		}else {
			$command->execute();
			return $decrease;
		}
	}
	
	/**
	 * check if $assertChild->fid equles $assertParent->id
	 * @param mixed $assertParent
	 * @param mixed $assertChild
	 */
	public function isParent($assertParent,$assertChild){
		$assertParent = $this->findByPk($assertParent);
		$assertChild = $this->findByPk($assertChild);
		if ( $assertParent !== null && $assertChild !== null ){
			return $assertParent->getAttribute('id') == $assertChild->getAttribute('fid');
		}else {
			return false;
		}
	}
	
	/**
	 * check if $a and $b have the same ancestor.
	 * @param mixed $a
	 * @param mixed $b
	 * @return boolean
	 */
	public function isRelative($a,$b){
		$a = $this->findByPk($a);
		$b = $this->findByPk($b);
		if ( $a !== null && $b !== null ){
			$aLft = $a->getAttribute('lft');
			$aRgt = $a->getAttribute('rgt');
			$bLft = $b->getAttribute('lft');
			$bRgt = $b->getAttribute('rgt');
			return ($aLft<$bLft && $aRgt>$bRgt) || ($aLft>$bLft && $aRgt<$bRgt);
		}else {
			return false;
		}
	}
	
	/**
	 * check if $a and $b is under the same level and have the same parent
	 * @param mixed $parent
	 * @param mixed $a
	 * @param mixed $b
	 * @return boolean
	 */
	public function isBrother($parent,$a,$b){
		$parent = $this->findByPk($parent);
		$a = $this->findByPk($a);
		$b = $this->findByPk($b);
		if ( $parent !== null && $a !== null && $b !== null ){
			$aLevel = $a->getAttribute('level');
			$bLevel = $b->getAttribute('level');
			return $aLevel === $bLevel && $this->isParent($parent,$a) && $this->isParent($parent,$b);
		}else {
			return false;
		}
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
		
		$fid = $parent->getPrimaryKey();
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
	 * @return CActiveRecord[]
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
	 * @return CActiveRecord[]
	 */
	public function findChildrenByParent($parent,$condition='',$params=array()){
		$parent = $this->findByPk($parent);
		if ( $parent === null ){
			return null;
		}
		$findCondition = "`fid`={$parent->getPrimaryKey()}";
		return $this->findChildren($findCondition,$condition,$params);
	}
	
	/**
	 * find children whose boundary is between $node->lft and $node->rgt.And returns a preorder tree.
	 * @param mixed $node CActiveRecord or int
	 * @return array[string,CActiveRecord] those record are orderd in lft ASC.
	 */
	public function findChildrenInPreorder($node){
		$node = $this->findByPk($node);
		if ( $node === null ){
			return null;
		}
		$findCondition = "`lft`>{$node->getAttribute('lft')} AND `rgt`<{$node->getAttribute('rgt')}";
		$children = $this->findChildren($findCondition,array('order'=>'`lft` ASC'));
		
		$preorderTree = array();
		$parentsMap = array();
		
		$preorderTree['n'.$node->getPrimaryKey()] = array('parent'=>null,'record'=>$node);
		$prev = $node;
		foreach ( $children as $child ){
			$nodeKey = 'n'.$child->getPrimaryKey();
			if ( $this->isParent($prev,$child) ){
				$parentKey = 'n'.$prev->getPrimaryKey();
				if ( !in_array($parentKey,$parentsMap) ){
					$parentsMap[] = $parentKey;
				}
			}else {
				$parentKey = 'n'.$child->getAttribute('fid');
			}
			$preorderTree[$nodeKey] = array('parent'=>$parentKey,'record'=>$child);
			$prev = $child;
		}
		
		return $preorderTree;
	}
	
	/**
	 * 
	 * @param string $findCondition
	 * @param mixed $criteria string or array or CDbCriteria
	 * @param array $params
	 * @return CActiveRecord[]
	 */
	public function findChildren($findCondition,$criteria='',$params=array()){
		if ( is_array($criteria) ){
			$criteria['condition'] = $findCondition;
		}elseif ( $criteria instanceof CDbCriteria ){
			$criteria->condition = $findCondition;
		}else {
			$criteria = $findCondition;
		}
		return $this->findAll($criteria,$params);
	}
}