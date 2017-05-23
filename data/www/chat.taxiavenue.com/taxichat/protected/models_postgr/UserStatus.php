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
 * @property boolean $is_activate
 * @property string $activate_key
 * @property boolean $mobile_os
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
			array('id_status, id_user', 'required'),
			array('id_status, id_user, moderation', 'numerical', 'integerOnly'=>true),
			array('lat, lng', 'numerical'),
			array('tokin_id, activate_key', 'length', 'max'=>255),
			array('location_update, status_update, is_activate, mobile_os', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, id_status, lat, lng, location_update, status_update, id_user, moderation, tokin_id, is_activate, activate_key, mobile_os', 'safe', 'on'=>'search'),
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
	
	public function RefreshTokin($tokin_id = null, $os = 1)
	{	
		if(!empty($tokin_id)) {
			$this->tokin_id = $tokin_id;
			if($os == 1) {
				$this->mobile_os = true;
			} else {
				$this->mobile_os = false;
			}	
			$this->save();
		}
		return $this;
	}
	
	public static function GetUserById($id_user = null) {
		$user = UserStatus::model()->findByAttributes(array('id_user' => $id_user));
		return $user;
	}
}
