<?php
/**
 * @name AuthManager.php UTF-8
 * @author ChenJunAn<lancelot1215@gmail.com>
 * 
 * Date 2013-8-14
 * Encoding UTF-8
 * 
 * @property RightCalculator $rightCalculator
 */
class AuthManager extends CApplicationComponent{
	/**
	 * use to access data object.such AuthRoles.
	 * @var string
	 */
	public $typePrefix = 'Auth';
	/**
	 * data object's name.
	 * @var string
	 */
	const GROUP 			= 'Groups';
	const OPERATION 		= 'Operation';
	const PERMISSION 		= 'Permission';
	const PROTECTED_TABLE 	= 'ProtectedTable';
	const RESOURCE 			= 'Resource';
	const RESOURCE_TYPE 	= 'ResourceType';
	const ROLE 				= 'Roles';
	
	const ACCESS_OPERATION 	= 'operationAccess';
	const ACCESS_RESOURCE 	= 'resourceAccess';
	
	/**
	 * @var RightCalculator
	 */
	private $_rightCalculator=null;
	
	public function init(){
		Yii::import('cms.modules.accessControl.components.*');
		Yii::import('cms.modules.accessControl.models.*');
	}
	
	/**
	 * @return RightCalculator
	 */
	public function getCalculator(){
		if ( $this->_rightCalculator === null ){
			$this->_rightCalculator = RightCalculator::getInstance();
		}
		return $this->_rightCalculator;
	}
	
	/**
	 * generate a series of record.
	 * @param array $allData
	 * @param string $type
	 * @return array. return FALSE if generate encounterd an error or @param $type is NULL.
	 */
	public function generateRecords($allData,$type=null){
		$result = array();
		
		foreach ( $allData as $data ){
			if ( isset($data['dataObjectType']) ){
				$dataObjectType = $data['dataObjectType'];
				unset($data['dataObjectType']);
			}elseif ( $type !== null ){
				$dataObjectType = $type;
			}else {
				return false;
			}
			
			$dataObject = $this->generate($dataObjectType,$data);
			if ( $dataObject === false ){//rollback
				foreach ( $result as $r ){
					foreach ( $r as $record ){
						$record->delete();
					}
				}
				return false;
			}else {
				$result[$dataObjectType][] = $dataObject;
			}
		}
		return $result;
	}
	
	/**
	 * generate a record.
	 * @param string $type
	 * @param array $data
	 * @return mixed
	 */
	public function generate($type,$data){
		$class = $this->typePrefix.$type;
		try {
			$object = new $class();
		}catch (Exception $e){
			return false;
		}
		
		$object->attributes = $data;
		if ( $object->validate() ){
			$object->save();
		}else {
			return false;
		}
		return $object;
	}
	
	/**
	 * check operation access
	 * @param array $operation
	 * @param int $uid
	 * @return mixed
	 */
	public function checkAccess($operation,$uid){
		if ( !is_array($operation) ){
			return false;
		}
		$module = $operation['module'];
		$controller = $operation['controller'];
		$action = $operation['action'];
		$op = AuthOperation::model()->with('AuthPermissions')->findUniqueRecord($module, $controller, $action);
		
		$opPermissions = $op->AuthPermisons;
		foreach ( $opPermissions as $opPermission ){
			$key = 'p'.$opPermission->getPrimaryKey();
			$unsafePermissions[$key] = $opPermission;
		}
		$userPermissions = $this->getCalculator()->run($uid);
		$intersect = array_intersect_assoc($unsafePermissions,$userPermissions);
		
		return empty($intersect) ? false : array('operation'=>$op,'permission'=>$intersect);
	}
}