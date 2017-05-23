<?php

/**
 * This is the model class for table "orders_points".
 *
 * The followings are the available columns in table 'orders_points':
 * @property integer $id
 * @property integer $id_order
 * @property integer $id_adress
 * @property string $entrance
 * @property integer $is_traversed
 */
class OrdersPoints extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'orders_points';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_order, id_adress', 'required'),
			array('id_order, id_adress, is_traversed', 'numerical', 'integerOnly'=>true),
			array('entrance', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, id_order, id_adress, entrance, is_traversed', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(			'order' => array(self::BELONGS_TO, 'Orders', 'id_order'),
			'adress' => array(self::BELONGS_TO, 'Addresses', 'id_adress'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'id_order' => 'Id Order',
			'id_adress' => 'Id Adress',
			'entrance' => 'Entrance',
			'is_traversed' => 'Is Traversed',
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
		$criteria->compare('id_order',$this->id_order);
		$criteria->compare('id_adress',$this->id_adress);
		$criteria->compare('entrance',$this->entrance,true);
		$criteria->compare('is_traversed',$this->is_traversed);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return OrdersPoints the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function GetPointInfo() {
		$point = $this->getAttributes();	
		
		$adress = array();
		if(!empty($this->adress)) {
			$adress = $this->adress->getAttributes();
		}
		
		return array_merge($point, $adress);
	}
}
