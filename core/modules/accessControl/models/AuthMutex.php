<?php

/**
 * This is the model class for table "{{auth_mutex}}".
 *
 * The followings are the available columns in table '{{auth_mutex}}':
 * @property string $role_one
 * @property string $role_two
 * @property string $description
 *
 * The followings are the available model relations:
 * @property AuthRoles $roleOne
 * @property AuthRoles $roleTwo
 */
class AuthMutex extends CmsActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{auth_mutex}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('role_one, role_two', 'required'),
			array('role_one, role_two', 'length', 'max'=>11),
			array('description', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('role_one, role_two, description', 'safe', 'on'=>'search'),
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
			'roleOne' => array(self::BELONGS_TO, 'AuthRoles', 'role_one'),
			'roleTwo' => array(self::BELONGS_TO, 'AuthRoles', 'role_two'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'role_one' => 'Role One',
			'role_two' => 'Role Two',
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

		$criteria->compare('role_one',$this->role_one,true);
		$criteria->compare('role_two',$this->role_two,true);
		$criteria->compare('description',$this->description,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AuthMutex the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
