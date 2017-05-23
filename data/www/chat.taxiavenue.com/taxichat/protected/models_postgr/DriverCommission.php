<?php

/**
 * This is the model class for table "driver_commission".
 *
 * The followings are the available columns in table 'driver_commission':
 * @property integer $id
 * @property integer $id_driver
 * @property string $value
 * @property string $descr
 * @property boolean $is_weekly
 */
class DriverCommission extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'driver_commission';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_driver, value', 'required'),
			array('id_driver', 'numerical', 'integerOnly'=>true),
			array('value', 'length', 'max'=>8),
			array('descr, is_weekly', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, id_driver, value, descr, is_weekly', 'safe', 'on'=>'search'),
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
			'value' => 'Value',
			'descr' => 'Descr',
			'is_weekly' => 'Is Weekly',
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
		$criteria->compare('value',$this->value,true);
		$criteria->compare('descr',$this->descr,true);
		$criteria->compare('is_weekly',$this->is_weekly);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return DriverCommission the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public static function UpdateRecord($id, $data = null)
	{
		if(!empty($data) && !empty($id)) {
			$commission = DriverCommission::model()->findByPk($id);
			if(!empty($commission)) {
				$commission->attributes = $data;
				$commission->save();
				
				return $commission;
			}
		} 
		return false;
	}
	
	public static function CreateRecord($id_driver, $data = null)
	{
		if(!empty($data) && !empty($id_driver)) {
			$commission = new DriverCommission;
			$commission->attributes = $data;
			$commission->id_driver = $id_driver;
			$commission->save();
			
			return $commission;
		} 
		return false;
	}
	
	public static function UpdateCommissions($commissions = null)
	{	
		if(!empty($commissions)) {	
			foreach($commissions as $id => $data) {
				self::UpdateRecord($id, $data);
			}
			return true;
		}
		return false;
	}
	
	public static function CreateCommissions($id_driver, $commissions = null)
	{	
		if(!empty($commissions) && !empty($id_driver)) {
			foreach($commissions as $data) {
				self::CreateRecord($id_driver, $data);
			}
			return true;
		}
		return false;
	}
}
