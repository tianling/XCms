<?php
/**
 * @name AuthProtectedTable.php UTF-8
 * @author ChenJunAn<lancelot1215@gmail.com>
 *
 * Date 2013-8-18
 * Encoding UTF-8
 */

/**
 * This is the model class for table "{{auth_protected_table}}".
 *
 * The followings are the available columns in table '{{auth_protected_table}}':
 * @property string $id
 * @property string $resource_type
 * @property string $table_name
 * @property string $field_name
 * @property string $description
 *
 * The followings are the available model relations:
 * @property AuthResourceType $resourceType
 */
class AuthProtectedTable extends CmsActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{auth_protected_table}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('resource_type, table_name', 'required'),
			array('resource_type', 'length', 'max'=>11),
			array('table_name', 'length', 'max'=>30),
			array('field_name', 'length', 'max'=>20),
			array('description', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, resource_type, table_name, field_name, description', 'safe', 'on'=>'search'),
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
			'resourceType' => array(self::BELONGS_TO, 'AuthResourceType', 'resource_type'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'resource_type' => 'Resource Type',
			'table_name' => 'Table Name',
			'field_name' => 'Field Name',
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
		$criteria->compare('resource_type',$this->resource_type,true);
		$criteria->compare('table_name',$this->table_name,true);
		$criteria->compare('field_name',$this->field_name,true);
		$criteria->compare('description',$this->description,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AuthProtectedTable the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
