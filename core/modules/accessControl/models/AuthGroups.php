<?php
/**
 * @name AuthGroups.php UTF-8
 * @author ChenJunAn<lancelot1215@gmail.com>
 *
 * Date 2013-8-18
 * Encoding UTF-8
 */

/**
 * This is the model class for table "{{auth_groups}}".
 *
 * The followings are the available columns in table '{{auth_groups}}':
 * @property string $id
 * @property string $group_name
 * @property string $description
 * @property integer $enabled
 * @property string $list_order
 *
 * The followings are the available model relations:
 * @property ApiUser[] $xcmsApiUsers
 * @property AuthRoles[] $xcmsAuthRoles
 * @property Community $community
 * @property User[] $xcmsUsers
 */
class AuthGroups extends CmsActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{auth_groups}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('group_name', 'required'),
			array('enabled', 'numerical', 'integerOnly'=>true),
			array('group_name', 'length', 'max'=>30),
			array('list_order', 'length', 'max'=>5),
			array('description', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, group_name, description, enabled, list_order', 'safe', 'on'=>'search'),
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
			'ApiUser' => array(self::MANY_MANY, 'ApiUser', '{{api_group}}(group_id, user_id)'),
			'AuthRole' => array(self::MANY_MANY, 'AuthRoles', '{{auth_gr}}(group_id, role_id)'),
			'Community' => array(self::HAS_ONE, 'Community', 'id'),
			'User' => array(self::MANY_MANY, 'User', '{{user_group}}(group_id, user_id)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'group_name' => 'Group Name',
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
		$criteria->compare('group_name',$this->group_name,true);
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
	 * @return AuthGroups the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
