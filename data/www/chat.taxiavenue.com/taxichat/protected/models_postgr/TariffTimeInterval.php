<?php

/**
 * This is the model class for table "tariff_time_interval".
 *
 * The followings are the available columns in table 'tariff_time_interval':
 * @property integer $id
 * @property string $name
 * @property string $from
 * @property string $before
 * @property boolean $is_percent
 * @property string $value
 */
class TariffTimeInterval extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tariff_time_interval';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, from, before, value', 'required'),
			array('name', 'length', 'max'=>255),
			array('value', 'length', 'max'=>8),
			array('is_percent', 'safe'),
			array('before', 'date_validate'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, from, before, is_percent, value', 'safe', 'on'=>'search'),
		);
	}

	public function date_validate($attribute)
	{
		if(strtotime($this->$attribute) - strtotime($this->from) <= 0)
			$this->addError($attribute, 'before должен превышать from');	
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
			'name' => 'Name',
			'from' => 'From',
			'before' => 'Before',
			'is_percent' => 'Is Percent',
			'value' => 'Value',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('from',$this->from,true);
		$criteria->compare('before',$this->before,true);
		$criteria->compare('is_percent',$this->is_percent);
		$criteria->compare('value',$this->value,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TariffTimeInterval the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
