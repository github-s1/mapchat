<?php

/**
 * This is the model class for table "orders_delay".
 *
 * The followings are the available columns in table 'orders_delay':
 * @property integer $id
 * @property integer $id_order
 * @property integer $adopted
 * @property integer $value
 */
class OrdersDelay extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'orders_delay';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_order, value', 'required'),
			array('id_order, adopted, value', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, id_order, adopted, value', 'safe', 'on'=>'search'),
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
			'id_order' => 'Id Order',
			'adopted' => 'Adopted',
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
		$criteria->compare('id_order',$this->id_order);
		$criteria->compare('adopted',$this->adopted);
		$criteria->compare('value',$this->value);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return OrdersDelay the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	// создает запрос на опоздание
	public static function Create($order_id, $value) {
		$order_delay = new OrdersDelay;
		$order_delay->id_order = $order_id;
		$order_delay->value = $value;
		$order_delay->adopted = 0;
		
		return $order_delay;
	}
	
	// подтверждает опоздание
	public static function ApplyDelay($order_id) {
		$request = false;
		$cancel = true;
		$order_delay = OrdersDelay::model()->findByAttributes(array('id_order' => $order_id), array('order'=>'id DESC'));	
		if(!empty($order_delay)) {
			if($order_delay->adopted == 0) {	
				$order_delay->adopted = 1;
				$order_delay->save();
				$cancel = false;
			}
			$request = true;
		} 
		return array('request' => $request, 'cancel' => $cancel,'order_delay' => $order_delay);
	}
}
