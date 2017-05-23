<?php

/**
 * This is the model class for table "driver_service_temp".
 *
 * The followings are the available columns in table 'driver_service_temp':
 * @property integer $id
 * @property integer $id_driver
 * @property integer $id_service
 */
class DriverServiceTemp extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'driver_service_temp';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_driver, id_service', 'required'),
			array('id_driver, id_service', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, id_driver, id_service', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'id_driver' => 'Id Driver',
			'id_service' => 'Id Service',
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
		$criteria->compare('id_driver',$this->id_driver);
		$criteria->compare('id_service',$this->id_service);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return DriverServiceTemp the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public static function CreateRecord($id_driver, $id_service)
	{	
		if(!empty($id_driver) && !empty($id_service)) {
			$service_dr = new DriverServiceTemp;
			$service_dr->id_driver = $id_driver;
			$service_dr->id_service = $id_service;
			$service_dr->save();
			
			return $service_dr;
		} 
		return false;
	}
	
	public static function UpdateServices($id_driver, $services = null, $is_mobile = false)
	{	
		
		DriverServiceTemp::model()->deleteAll('id_driver = ?' , array($id_driver));
		if(!empty($services)) {	
			foreach($services as $id => $val) {
				if($is_mobile) {
					self::CreateRecord($id_driver, $val);
				} else {
					self::CreateRecord($id_driver, $id);
				}
			}
			return true;
		}
		return false;
	}
}
