<?php

/**
 * This is the model class for table "price_class".
 *
 * The followings are the available columns in table 'price_class':
 * @property integer $id
 * @property string $name
 * @property boolean $is_percent
 * @property string $value
 */
class PriceClass extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'price_class';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('name', 'unique'),
			array('name', 'length', 'max'=>255),
			array('value', 'length', 'max'=>8),
			array('is_percent', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, is_percent, value', 'safe', 'on'=>'search'),
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
			'name' => 'Ценовой класс',
			'is_percent' => 'Is Percent',
			'value' => 'Величина',
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
	 * @return PriceClass the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public static function GetAll($is_mobile = false) {
		$price_class_all = PriceClass::model()->findAll(array('order'=>'id ASC'));
		
		if($is_mobile) {
			$price_class_name_all = array();
			$price_class_index_all = array();
			if(!empty($price_class_all)) {
				foreach($price_class_all as $pr) {
					$price_class_name_all[] = $pr->name;
					$price_class_index_all[] = $pr->id;
				}
			}
			return array('names' => $price_class_name_all, 'indexes' => $price_class_index_all);
		} else {
			$price_class_all = CHtml::listData($price_class_all, 'id', 'name');
			
			return $price_class_all;
		}
		
	}
}
