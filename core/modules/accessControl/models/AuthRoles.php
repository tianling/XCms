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
class AuthRoles extends LevelModel
{
	
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
			'ApiUser' => array(self::MANY_MANY, 'ApiUser', '{{api_role}}(role_id, user_id)'),
			'AuthGroup' => array(self::MANY_MANY, 'AuthGroups', '{{auth_group_role}}(role_id, group_id)'),
			'AuthMutex1' => array(self::HAS_MANY, 'AuthMutex', 'role_one'),
			'AuthMutex2' => array(self::HAS_MANY, 'AuthMutex', 'role_two'),
			'AuthPermission' => array(self::MANY_MANY, 'AuthPermission', '{{auth_role_permission}}(role_id, permission_id)'),
			'AuthUser' => array(self::MANY_MANY, 'User', '{{user_role}}(role_id, user_id)'),
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
}
