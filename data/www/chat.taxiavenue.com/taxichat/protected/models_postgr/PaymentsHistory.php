<?php

/**
 * This is the model class for table "payments_history".
 *
 * The followings are the available columns in table 'payments_history':
 * @property integer $id
 * @property integer $id_user
 * @property integer $id_type
 * @property string $value
 * @property string $balance
 * @property string $descr
 * @property integer $id_order
 * @property string $date_create
 * @property string $rating
 */
class PaymentsHistory extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'payments_history';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_user, id_type, value, balance, rating', 'required'),
			array('id_user, id_type, id_order', 'numerical', 'integerOnly'=>true),
			array('value, balance', 'length', 'max'=>8),
			array('rating', 'length', 'max'=>4),
			array('descr, date_create', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, id_user, id_type, value, balance, descr, id_order, date_create, rating', 'safe', 'on'=>'search'),
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
			'type' => array(self::BELONGS_TO, 'TypesOperations', 'id_type'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'id_user' => 'Id User',
			'id_type' => 'Id Type',
			'value' => 'Сумма',
			'balance' => 'Баланс',
			'descr' => 'Описание',
			'id_order' => 'Id Order',
			'date_create' => 'Дата',
			'rating' => 'Рейтинг',
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
		$criteria->compare('id_user',$this->id_user);
		$criteria->compare('id_type',$this->id_type);
		$criteria->compare('value',$this->value,true);
		$criteria->compare('balance',$this->balance,true);
		$criteria->compare('descr',$this->descr,true);
		$criteria->compare('id_order',$this->id_order);
		$criteria->compare('date_create',$this->date_create,true);
		$criteria->compare('rating',$this->rating,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PaymentsHistory the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function GetPaymentInfo()
	{
		$result = $this->getAttributes();
		if(!empty($this->type)) {
			$result['type'] = $this->type->name;
		}
		return $result;
	}
	
	public static function PaymentOperation($driver_id, $id_type, $balance, $rating, $comission, $id_order = null, $descr = null )
	{
		$new_fine = new PaymentsHistory;
		$new_fine->id_user = $driver_id;
		$new_fine->id_type = $id_type;
		$new_fine->balance = $balance;
		$new_fine->rating = $rating;
		$new_fine->value = $comission;
		$new_fine->id_order = $id_order;
		$new_fine->descr = $descr;
		$new_fine->date_create = date('Y-m-d H:i:s');
		$new_fine->save();
		
		return $new_fine;
	}
	
	public static function RemoveCommission($driver_id, $balance, $rating, $comission, $is_daily = true )
	{
		if($is_daily) {
			$id_type = 1;
		} else {
			$id_type = 2;
		}
		
		$new_fine = self::PaymentOperation($driver_id, $id_type, $balance, $rating, -$comission);
		return $new_fine;
	}
	
	public static function RemoveOrderCommission($driver_id, $balance, $rating, $comission, $id_order )
	{	
		$new_fine = self::PaymentOperation($driver_id, 3, $balance, $rating, -$comission);
		
		return $new_fine;
	}
	
	public static function RemoveFine($driver_id, $balance, $rating, $comission, $descr)
	{
		
		$new_fine = self::PaymentOperation($driver_id, 4, $balance, $rating, -$comission, null, $descr);

		return $new_fine;
	}
	
	public static function Depositing($driver_id, $id_type, $balance, $rating, $comission, $descr, $id_order = null)
	{
		
		$new_fine = self::PaymentOperation($driver_id, $id_type, $balance, $rating, $comission, $id_order, $descr);

		return $new_fine;
	}
	
	
}
