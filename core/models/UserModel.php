<?php

/**
 * This is the model class for table "{{user}}".
 *
 * The followings are the available columns in table '{{user}}':
 * @property string $id
 * @property string $nickname
 * @property string $realname
 * @property string $email
 * @property string $password
 * @property string $salt
 * @property string $last_login_time
 * @property string $last_login_ip
 * @property integer $locked
 *
 * The followings are the available model relations:
 * @property AdViewClick[] $adViewClicks
 * @property Administrators $administrators
 * @property AuthPermission[] $AuthPermissions
 * @property ChatRoom[] $ChatRooms
 * @property ChatMessage[] $chatMessages
 * @property ChatRoom[] $ChatRooms1
 * @property Groups[] $Groups
 * @property GroupMessage[] $groupMessages
 * @property Groups[] $Groups1
 * @property Groups[] $groups
 * @property ChatMessage[] $ChatMessages
 * @property GroupMessage[] $GroupMessages
 * @property SqbUser $sqbUser
 * @property UserBlacklist[] $userBlacklists
 * @property UserBlacklist[] $userBlacklists1
 * @property AuthGroups[] $AuthGroups
 * @property UserInterest[] $userInterests
 * @property UserInterest[] $userInterests1
 * @property ChatRoom[] $ChatRooms2
 * @property Groups[] $Groups2
 * @property UserReport[] $userReports
 * @property AuthRoles[] $AuthRoles
 * @property UserTrends[] $userTrends
 * @property UserTrendsReply[] $userTrendsReplies
 * @property UserTrends[] $UserTrends
 */
class UserModel extends SingleInheritanceModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{user}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('nickname, password', 'required'),
			array('last_login_time, last_login_ip', 'required', 'on'=>'update'),
			array('locked', 'numerical', 'integerOnly'=>true),
			array('nickname', 'length', 'max'=>20),
			array('realname', 'length', 'max'=>5),
			array('password', 'length', 'max'=>255),
			array('salt', 'length', 'max'=>128),
			array('last_login_time', 'length', 'max'=>11),
			array('last_login_ip', 'length', 'max'=>15),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, nickname, realname, password, salt, last_login_time, last_login_ip, locked', 'safe', 'on'=>'search'),
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
			'adViewClicks' => array(self::HAS_MANY, 'AdViewClick', 'user_id'),
			'administrators' => array(self::HAS_ONE, 'Administrators', 'id'),
			'AuthPermissions' => array(self::MANY_MANY, 'AuthPermission', '{{auth_user_permission}}(user_id, permission_id)'),
			'ChatRooms' => array(self::MANY_MANY, 'ChatRoom', '{{chat_admin}}(user_id, room_id)'),
			'chatMessages' => array(self::HAS_MANY, 'ChatMessage', 'sender'),
			'ChatRooms1' => array(self::MANY_MANY, 'ChatRoom', '{{chat_shielded}}(user_id, room_id)'),
			'Groups' => array(self::MANY_MANY, 'Groups', '{{group_admin}}(user_id, group_id)'),
			'groupMessages' => array(self::HAS_MANY, 'GroupMessage', 'sender'),
			'Groups1' => array(self::MANY_MANY, 'Groups', '{{group_shielded}}(user_id, group_id)'),
			'groups' => array(self::HAS_MANY, 'Groups', 'master_id'),
			'ChatMessages' => array(self::MANY_MANY, 'ChatMessage', '{{offline_chat_message}}(user_id, msg_id)'),
			'GroupMessages' => array(self::MANY_MANY, 'GroupMessage', '{{offline_group_message}}(user_id, msg_id)'),
			'sqbUser' => array(self::HAS_ONE, 'SqbUser', 'id'),
			'userBlacklists' => array(self::HAS_MANY, 'UserBlacklist', 'user_id'),
			'userBlacklists1' => array(self::HAS_MANY, 'UserBlacklist', 'black_user_id'),
			'AuthGroups' => array(self::MANY_MANY, 'AuthGroups', '{{user_group}}(user_id, group_id)'),
			'userInterests' => array(self::HAS_MANY, 'UserInterest', 'follower'),
			'userInterests1' => array(self::HAS_MANY, 'UserInterest', 'followed'),
			'ChatRooms2' => array(self::MANY_MANY, 'ChatRoom', '{{user_own_chat}}(user_id, room_id)'),
			'Groups2' => array(self::MANY_MANY, 'Groups', '{{user_own_group}}(user_id, group_id)'),
			'userReports' => array(self::HAS_MANY, 'UserReport', 'user_id'),
			'AuthRoles' => array(self::MANY_MANY, 'AuthRoles', '{{user_role}}(user_id, role_id)'),
			'userTrends' => array(self::HAS_MANY, 'UserTrends', 'user_id'),
			'userTrendsReplies' => array(self::HAS_MANY, 'UserTrendsReply', 'user_id'),
			'UserTrends' => array(self::MANY_MANY, 'UserTrends', '{{user_trends_support}}(user_id, trends_id)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'nickname' => 'Nickname',
			'realname' => 'Realname',
			'email' => 'Email',
			'password' => 'Password',
			'salt' => 'Salt',
			'last_login_time' => 'Last Login Time',
			'last_login_ip' => 'Last Login Ip',
			'locked' => 'Locked',
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
		$criteria->compare('nickname',$this->nickname,true);
		$criteria->compare('realname',$this->realname,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('salt',$this->salt,true);
		$criteria->compare('last_login_time',$this->last_login_time,true);
		$criteria->compare('last_login_ip',$this->last_login_ip,true);
		$criteria->compare('locked',$this->locked);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	protected function beforeSave(){
		$password = $this->getAttribute('password');
		$passwordManager = Yii::app()->getComponent('passwordManager');
		$new = $passwordManager->generate($password);
		$this->setAttribute('password',$new['password']);
		return parent::beforeSave();
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
