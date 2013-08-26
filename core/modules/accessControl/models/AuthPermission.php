<?php
/**
 * @name AuthPermission.php UTF-8
 * @author ChenJunAn<lancelot1215@gmail.com>
 *
 * Date 2013-8-18
 * Encoding UTF-8
 */

/**
 * This is the model class for table "{{auth_permission}}".
 *
 * The followings are the available columns in table '{{auth_permission}}':
 * @property string $id
 * @property string $operation_id
 * @property string $resource_id
 * @property string $permission_name
 * @property string $description
 *
 * The followings are the available model relations:
 * @property AuthOperation $function
 * @property XmcsAuthResource $resource
 * @property AuthRoles[] $xcmsAuthRoles
 * @property User[] $xcmsUsers
 */
class AuthPermission extends CmsActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{auth_permission}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('operation_id, permission_name', 'required'),
			array('operation_id, resource_id', 'length', 'max'=>11),
			array('permission_name', 'length', 'max'=>20),
			array('description', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, operation_id, resource_id, permission_name, description', 'safe', 'on'=>'search'),
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
			'operation' => array(self::BELONGS_TO, 'AuthOperation', 'operation_id'),
			'resource' => array(self::BELONGS_TO, 'XmcsAuthResource', 'resource_id'),
			'authRole' => array(self::MANY_MANY, 'AuthRoles', '{{auth_role_permission}}(permission_id, role_id)'),
			'user' => array(self::MANY_MANY, 'User', '{{auth_user_permission}}(permission_id, user_id)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'operation_id' => 'Function',
			'resource_id' => 'Resource',
			'permission_name' => 'Permission Name',
			'description' => 'Description',
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
		$criteria->compare('operation_id',$this->operation_id,true);
		$criteria->compare('resource_id',$this->resource_id,true);
		$criteria->compare('permission_name',$this->permission_name,true);
		$criteria->compare('description',$this->description,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AuthPermission the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
