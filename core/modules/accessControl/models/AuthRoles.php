<?php
/**
 * @name AuthRoles.php UTF-8
 * @author ChenJunAn<lancelot1215@gmail.com>
 *
 * Date 2013-8-18
 * Encoding UTF-8
 */

/**
 * This is the model class for table "{{auth_roles}}".
 *
 * The followings are the available columns in table '{{auth_roles}}':
 * @property string $id
 * @property string $fid
 * @property string $level
 * @property string $lft
 * @property string $rgt
 * @property string $role_name
 * @property string $description
 * @property integer $enabled
 * @property string $list_order
 *
 * The followings are the available model relations:
 * @property ApiUser[] $apiUsers
 * @property AuthGroups[] $authGroups
 * @property AuthMutex[] $authMutex1
 * @property AuthMutex[] $authMutex2
 * @property AuthPermission[] $authPermissions
 * @property User[] $authUsers
 */
class AuthRoles extends CmsActiveRecord
{
	/**
	 * when save/update.use this method to update preorder tree.
	 * @var int
	 */
	const PREORDER_TREE_SAVE = 0;
	/**
	 * when delete.use this method to update preorder tree.
	 * @var unknown
	 */
	const PREORDER_TREE_DELETE = 1;
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{auth_roles}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('role_name', 'required'),
			array('fid, level, lft, rgt', 'length', 'max'=>11),
			array('role_name', 'length', 'max'=>30),
			array('list_order', 'length', 'max'=>5),
			array('description,fid, level, lft, rgt,enabled', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, fid, level, lft, rgt, role_name, description, enabled, list_order', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'apiUser' => array(self::MANY_MANY, 'ApiUser', '{{api_role}}(id, user_id)'),
			'authGroup' => array(self::MANY_MANY, 'AuthGroups', '{{auth_gr}}(role_id, group_id)'),
			'authMutex1' => array(self::HAS_MANY, 'AuthMutex', 'role_one'),
			'authMutex2' => array(self::HAS_MANY, 'AuthMutex', 'role_two'),
			'authPermission' => array(self::MANY_MANY, 'AuthPermission', '{{auth_role_permission}}(role_id, permission_id)'),
			'authUser' => array(self::MANY_MANY, 'User', '{{user_role}}(role_id, user_id)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'fid' => 'Fid',
			'level' => 'Level',
			'lft' => 'Lft',
			'rgt' => 'Rgt',
			'role_name' => 'Role Name',
			'description' => 'Description',
			'enabled' => 'Enabled',
			'list_order' => 'List Order',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('fid',$this->fid,true);
		$criteria->compare('level',$this->level,true);
		$criteria->compare('lft',$this->lft,true);
		$criteria->compare('rgt',$this->rgt,true);
		$criteria->compare('role_name',$this->role_name,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('enabled',$this->enabled);
		$criteria->compare('list_order',$this->list_order,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AuthRoles the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * get left or right pole.
	 * @param string $type
	 * @return int
	 */
	public function getPole($type){
		$order = $type === 'lft' ? ' ASC' : ' DESC';
		$criteria = new CDbCriteria();
		$criteria->select = $type.' AS pole';
		$criteria->order = $type.$order;
		$criteria->limit = 1;
		$criteria->offset = 0;
		$result = $this->findAll($criteria);
		
		return !empty($result) ? $result[0]['pole'] : null;
	}
	
	protected function beforeSave(){
		if ( $this->getIsNewRecord() ){//insert
			if ( ($attributes = $this->getAttributesBeforeSave()) === false ){
				return false;
			}
			$table = $this->getMetaData()->tableSchema->getTable($this->tableName());
			$sql = "UPDATE `{$table}` SET `lft`=`lft`+2,`rgt`=`rgt`+2 WHERE `lft`>={$attributes['lft']};".
			"UPDATE `{$table}` SET `rgt`=`rgt`+2 WHERE `lft`<{$attributes['lft']} AND `rgt`>{$attributes['rgt']};";
			$this->getDbConnection()->createCommand($sql)->execute();
		}else {//update
			
		}
		
		$attributes['enabled'] = 1 & $this->getAttribute('enabled');
		$this->setAttributes($attributes);
		return parent::beforeSave();
	}
	
	/**
	 * get fid,lft,rgt,level before save
	 * @return array
	 */
	public function getAttributesBeforeSave(){
		$attributes = array();
		if ( $this->getIsNewRecord() ){//on create
			$fid = $this->getAttribute('fid');
			if ( $fid == 0 || $fid === null ){
				$attributes = array('fid'=>0,'level'=>1);
				$lastRight = $this->getPole('rgt');
				if ( $lastRight === null ){
					$lastRight = 0;
				}
			}else {
				$parent = $this->findByPk($fid);
				if ( $parent === null ){
					$this->addError('fid',Yii::t('auth','parent role does not exist'));
					return false;
				}
				$attributes = array('fid'=>$fid,'level'=>$parent->getAttribute('level')+1);
				$lastRight = $parent->getAttribute('rgt');
			}
			$attributes['lft'] = $lastRight + 1;
			$attributes['rgt'] = $lastRight + 2;
		}else {//on update
			$id = $this->getAttribute('id');
			$old = $this->findByPk($id);
			if ( $old === null ){
				$this->addError('id',Yii::t('auth','the role you want to edit is not exsist'));
				return false;
			}
			$fid = $this->getAttribute('fid');
			if ( $fid !== $id ){//parent changed
				
			}
		}
		return $attributes;
	}
	
	public function getChildren(){
		
	}
	
	protected function beforeDelete(){
		
		return parent::beforeDelete();
	}
	
	public function deleteAll(){
		
	}
	
	public function updateRange($method,$left,$right,$num = 2){
		$table = $this->getMetaData()->tableSchema->getTable($this->tableName());
		
		if ( $method == 'update' ){
			$voluationOperator = ' + ';
			$conditionOperator = " >= ";
		}elseif ( $method == 'delete' ){
			$voluationOperator = ' - ';
			$conditionOperator = " > ";
		}else{
			return false;
		}
		$sql = "UPDATE `{$table}` SET `lft`=`lft`{$voluationOperator}{$num} WHERE `lft`{$conditionOperator}{$left};".
		"UPDATE `{$table}` SET `rgt`=`rgt`{$voluationOperator}{$num} WHERE `rgt`{$conditionOperator}{$left}";
	
		$this->db->createCommand($sql)->execute();
		return true;
	}
	
	/**
	 * Update preorder tree.
	 * The table must conatins fields `lft` and `rgt`.
	 * @param int $method stands for save/update or delete.
	 * @param mixed $subtreeRoot can be an integer or an CActiveRecord.
	 * @param mixed $targetNode can be an integer or an CActiveRecord.NULL if the $method is delete.
	 * @return boolean
	 */
	public function updatePreorderTree($method,$subtreeRoot,$targetNode=null){
		$db = $this->getDbConnection();
		$table = $this->getMetaData()->tableSchema->getTable($this->tableName());
		
		if ( $method === self::PREORDER_TREE_SAVE ){
			$operator = '+';
			$conditionOperator = '>=';
		}elseif ( $method === self::PREORDER_TREE_DELETE ){
			$operator = '-';
			$conditionOperator = '>';
		}else {
			return false;
		}
		
		if ( ! $subtreeRoot instanceof CActiveRecord ){
			$subtreeRoot = $this->findByPk($subtreeRoot);
		}
		if ( ! $targetNode instanceof CActiveRecord ){
			$targetNode = $this->findByPk($targetNode);
		}
		
		$countSql = "SELECT COUNT(*) FROM `{$table}` WHERE `lft`>{$subtreeRoot->lft} AND `rgt`<{$subtreeRoot->rgt}";
		$change = 2 * $this->countBySql($countSql);
		
	}
}
