<?php

/**
 * This is the model class for table "user_status".
 *
 * The followings are the available columns in table 'user_status':
 * @property integer $id
 * @property integer $id_status
 * @property double $lat
 * @property double $lng
 * @property string $location_update
 * @property string $status_update
 * @property integer $id_user
 * @property integer $moderation
 * @property string $tokin_id
 * @property integer $is_activate
 * @property string $activate_key
 * @property integer $mobile_os
 * @property string $last_push_params
 */
class UserStatus extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user_status';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_status, location_update, status_update, id_user', 'required'),
			array('id_status, id_user, moderation, is_activate, mobile_os', 'numerical', 'integerOnly'=>true),
			array('lat, lng', 'numerical'),
			array('tokin_id, activate_key', 'length', 'max'=>255),
			array('last_push_params', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, id_status, lat, lng, location_update, status_update, id_user, moderation, tokin_id, is_activate, activate_key, mobile_os, last_push_params', 'safe', 'on'=>'search'),
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
					'user' => array(self::BELONGS_TO, 'Users', 'id_user'),
            'status' => array(self::BELONGS_TO, 'Statuses', 'id_status'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'id_status' => 'Cтатус',
			'lat' => 'Широта',
			'lng' => 'Долгота',
			'location_update' => 'Location Update',
			'status_update' => 'Status Update',
			'id_user' => 'Id User',
			'moderation' => 'Moderation',
			'tokin_id' => 'Tokin',
			'is_activate' => 'Is Activate',
			'activate_key' => 'Activate Key',
			'mobile_os' => 'Mobile Os',
			'last_push_params' => 'Last Push Params',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('id_status',$this->id_status);
		$criteria->compare('lat',$this->lat);
		$criteria->compare('lng',$this->lng);
		$criteria->compare('location_update',$this->location_update,true);
		$criteria->compare('status_update',$this->status_update,true);
		$criteria->compare('id_user',$this->id_user);
		$criteria->compare('moderation',$this->moderation);
		$criteria->compare('tokin_id',$this->tokin_id,true);
		$criteria->compare('is_activate',$this->is_activate);
		$criteria->compare('activate_key',$this->activate_key,true);
		$criteria->compare('mobile_os',$this->mobile_os);
		$criteria->compare('last_push_params',$this->last_push_params,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserStatus the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	// сеттер свойств класса
	public function SetProperties($properties = null)
	{	
		if(!empty($properties)) {
			/*
			foreach($properties as $i => $element) {
				if(empty($element)) {
					unset($properties[$i]);
				}
			}
			*/
			$this->attributes = $properties;
		}
		return $this;
	}
	
	// меняет статус пользователя
	public function ChangeStatus($moderation = null, $id_status = null)
	{	
		if($moderation !== null) {
			$this->moderation = $moderation;
		} 
		if($id_status !== null) {
			$this->id_status = $id_status;
			$this->status_update = date('Y-m-d H:i:s', strtotime("now"));
		}
		
		$this->save();
		
		return $this;
	}
	
	// записывает текущие координаты водителя
	public function ChangeLocation($lat = null, $lng = null)
	{	
		if(!empty($lat) && !empty($lng)) {
			$this->lat = $lat;
			$this->lng = $lng;
			$this->location_update = date('Y-m-d H:i:s', strtotime("now"));
			$this->save();
		}
		return $this;
	}
	
	// записывает токин телефона пользователя
	public function RefreshTokin($tokin_id = null, $os = 1)
	{	
		if(!empty($tokin_id)) {
			$this->tokin_id = $tokin_id;
			$this->mobile_os = $os;	
			$this->save();
		}
		return $this;
	}
	
	// возвращает данные пользователя по id
	public static function GetUserById($id_user = null) {
		$user = UserStatus::model()->findByAttributes(array('id_user' => $id_user));
		return $user;
	}
	
	// отправляет пушь пользователю
	// $params - массив передваемых параметров
	// $save_flag - сохранять ли данные этого пуша как последнего
	public function SendPush($massage, array $params, $save_flag = true) {
		if(!empty($this->tokin_id) && !empty($massage) && isset($params['push_type'])) {
			$app_flag = true;
			if($this->user->id_type == 2) {
				$app_flag = false;
			}
			$push = new Push($app_flag, $this->tokin_id, $this->mobile_os);
			$push->setMassage($massage);
			if(!empty($params)) {
				foreach($params as $idx => $val) {
					$push->setValue($idx, $val);
				}
			}
			$res = $push->sendPush();
			if($save_flag) {
				$this->last_push_params	= serialize($params);
				$this->save();
			}
			return $res;
		}
		return false;
	}
	
	// возвращает данные последнего пуша пользователя
	public function GetLastPush() {
		if(!empty($this->last_push_params)) {
			$push_info = $this->last_push_params;
			$this->last_push_params = null;
			$this->save();
			return unserialize($push_info);
		} else {
			return 0;
		}
	}
	
	// возвращает не активированых пользователей
	public static function GetInactiveUsers() {		
		$users = UserStatus::model()->findAllByAttributes(array('is_activate' => 0), array('order'=>'id ASC'));
		
		return $users;
	}	
}
