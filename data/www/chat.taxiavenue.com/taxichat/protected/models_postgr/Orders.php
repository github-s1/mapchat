<?php

/**
 * This is the model class for table "orders".
 *
 * The followings are the available columns in table 'orders':
 * @property integer $id
 * @property string $order_date
 * @property string $price
 * @property string $distance
 * @property integer $id_customer
 * @property integer $id_driver
 * @property string $driver_note
 * @property integer $id_creator
 * @property integer $from
 * @property integer $id_status
 * @property integer $id_price_class
 * @property string $additional_info
 * @property string $driver_commission
 * @property string $price_distance
 * @property integer $where
 * @property boolean $is_preliminary
 * @property integer $id_parent
 * @property integer $execution_status
 * @property string $price_without_class
 * @property string $change_date
 * @property boolean $is_archive_delete
 * @property string $bonuses
 * @property string $custom_route
 * @property integer $termination_point
 * @property boolean $is_custom_route
 * @property integer $down_time
 * @property boolean $is_show_free_ester
 * @property string $operator_note
 * @property string $phone_note
 * @property integer $idle_city_distance
 * @property integer $idle_outside_city_distance
 * @property boolean $is_customer_chose
 * @property boolean $is_pay_bonuses
 * @property string $price_without_bonuses
 * @property string $income
 * @property string $commission
 * @property boolean $is_use_commission
 */
class Orders extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public $review;  
	public function tableName()
	{
		return 'orders';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('order_date, id_customer, id_creator, change_date', 'required'),
			array('id_customer, id_driver, id_creator, from, id_status, id_price_class, where, id_parent, execution_status, termination_point, down_time, idle_city_distance, idle_outside_city_distance', 'numerical', 'integerOnly'=>true),
			array('price, driver_commission, price_distance, price_without_class, bonuses, price_without_bonuses, income, commission', 'length', 'max'=>8),
			array('distance', 'length', 'max'=>9),
			array('order_date', 'date_validate'),
			array('driver_note, additional_info, is_preliminary, is_archive_delete, custom_route, is_custom_route, is_show_free_ester, operator_note, phone_note, is_customer_chose, is_pay_bonuses, is_use_commission', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, order_date, price, distance, id_customer, id_driver, driver_note, id_creator, from, id_status, id_price_class, additional_info, driver_commission, price_distance, where, is_preliminary, id_parent, execution_status, price_without_class, change_date, is_archive_delete, bonuses, custom_route, termination_point, is_custom_route, down_time, is_show_free_ester, operator_note, phone_note, idle_city_distance, idle_outside_city_distance, is_customer_chose, is_pay_bonuses, price_without_bonuses, income, commission, is_use_commission', 'safe', 'on'=>'search'),
		);
	}
	
	public function date_validate($attribute)
	{
		//echo(strtotime($this->$attribute).'   '.strtotime("now") ); exit;
		/*
		if(strtotime($this->$attribute) - strtotime("now") <= -2000)
			$this->addError($attribute, 'Дата/время заказа должны превышать текущие');
		*/

		if($this->is_preliminary == 1) {
			$orders = Orders::model()->findAllByAttributes(array('id_customer' => $this->id_customer, 'is_preliminary' => true, 'execution_status' => 1), array('order'=>'id ASC'));

			if(!empty($orders)) {
				foreach($orders as $ord) {
					if(empty($this->id) || (!empty($this->id) && $this->id != $ord->id)) {
						$start_time = strtotime($this->$attribute) - 3600;
						$end_time = strtotime($this->$attribute) + 3600;
						$ord_time = strtotime($ord->order_date);

						if($ord_time > $start_time && $ord_time < $end_time) {
							$this->addError($attribute, 'Временной промежуток между предварительными заказами клиента должен превышать 1 час');
							break;
						}
					}
				}
			}
		} 
		/*
		else {
			$orders = Orders::model()->findAllByAttributes(array('id_customer' => $this->id_customer, 'is_preliminary' => false, 'execution_status' => 1), array('order'=>'id ASC'));
			if(!empty($orders)) {
				foreach($orders as $ord) {
					if(empty($this->id) || (!empty($this->id) && $this->id != $ord->id)) {
						$this->addError($attribute, 'Допускается создание лишь одного текущего заказа.');
						break;
					}
				}

			}
		}
		*/
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'customer' => array(self::BELONGS_TO, 'Users', 'id_customer'),			
			'driver' => array(self::BELONGS_TO, 'Users', 'id_driver'),
			'creator' => array(self::BELONGS_TO, 'Users', 'id_creator'),
			'from_adress' => array(self::BELONGS_TO, 'OrdersPoints', 'from'),
			'where_adress' => array(self::BELONGS_TO, 'OrdersPoints', 'where'),
			'status' => array(self::BELONGS_TO, 'OrderStatuses', 'id_status'),
			'execut_status' => array(self::BELONGS_TO, 'ExecutionStatuses', 'execution_status'),
			'price_class' => array(self::BELONGS_TO, 'PriceClass', 'id_price_class'),
			'services'=>array(self::HAS_MANY, 'OrderService', 'id_order'),
			'termination_adress' => array(self::BELONGS_TO, 'Addresses', 'termination_point'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'order_date' => 'Дата/время заказа',
			'price' => 'Цена',
			'distance' => 'Расстояние',
			'id_customer' => 'Заказчик',
			'id_driver' => 'Водитель',
			'driver_note' => 'Заметка водителя',
			'id_creator' => 'Id Creator',
			'from' => 'Откуда',
			'id_status' => 'Статус',
			'id_price_class' => 'Ценовой класс',
			'additional_info' => 'Уточнение адреса',
			'driver_commission' => 'Комиссия водителя',
			'price_distance' => 'Цена расстояния',
			'where' => 'Куда',
			'is_preliminary' => 'Предварительный',
			'id_parent' => 'Id Parent',
			'execution_status' => 'Execution Status',
			'price_without_class' => 'Price Without Class',
			'change_date' => 'Change Date',
			'is_archive_delete' => 'Is Archive Delete',
			'bonuses' => 'Bonuses',
			'custom_route' => 'Custom Route',
			'termination_point' => 'Termination Point',
			'is_custom_route' => 'Is Custom Route',
			'down_time' => 'Down Time',
			'is_show_free_ester' => 'Is Show Free Ester',
			'operator_note' => 'Operator Note',
			'phone_note' => 'Phone Note',
			'idle_city_distance' => 'Idle City Distance',
			'idle_outside_city_distance' => 'Idle Outside City Distance',
			'is_customer_chose' => 'Is Customer Chose',
			'is_pay_bonuses' => 'Is Pay Bonuses',
			'price_without_bonuses' => 'Price Without Bonuses',
			'income' => 'Income',
			'commission' => 'Commission',
			'is_use_commission' => 'Is Use Commission',
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
		if(!empty($_GET)) {
			if(!empty($_GET['driver'])) {
				$criteria->with = array('driver'=>array('select'=>false,'joinType'=>'INNER JOIN','condition'=>'driver.phone LIKE :driver OR driver.name LIKE :driver OR driver.surname LIKE :driver OR driver.patronymic LIKE :driver OR driver.email LIKE :driver OR driver.nickname LIKE :driver','params'=>array(':driver'=>'%'.$_GET['driver'].'%')));
			}
			if(!empty($_GET['date_from'])) {
				$criteria->addCondition("order_date > '".date('Y-m-d', strtotime($_GET['date_from']))."'");
			}
			if(!empty($_GET['date_to'])) {
				$criteria->addCondition("order_date < '".date('Y-m-d', strtotime($_GET['date_to']))."'");
			}
		}

		$criteria->compare('id',$this->id);
		$criteria->compare('order_date',$this->order_date,true);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('distance',$this->distance,true);
		$criteria->compare('id_customer',$this->id_customer);
		$criteria->compare('id_driver',$this->id_driver);
		$criteria->compare('driver_note',$this->driver_note,true);
		$criteria->compare('id_creator',$this->id_creator);
		$criteria->compare('from',$this->from);
		$criteria->compare('id_status',$this->id_status);
		$criteria->compare('id_price_class',$this->id_price_class);
		$criteria->compare('additional_info',$this->additional_info,true);
		$criteria->compare('driver_commission',$this->driver_commission,true);
		$criteria->compare('price_distance',$this->price_distance,true);
		$criteria->compare('where',$this->where);
		$criteria->compare('is_preliminary',$this->is_preliminary);
		$criteria->compare('id_parent',$this->id_parent);
		$criteria->compare('execution_status',$this->execution_status);
		$criteria->compare('price_without_class',$this->price_without_class,true);
		$criteria->compare('change_date',$this->change_date,true);
		$criteria->compare('is_archive_delete',$this->is_archive_delete);
		$criteria->compare('bonuses',$this->bonuses,true);
		$criteria->compare('custom_route',$this->custom_route,true);
		$criteria->compare('termination_point',$this->termination_point);
		$criteria->compare('is_custom_route',$this->is_custom_route);
		$criteria->compare('down_time',$this->down_time);
		$criteria->compare('is_show_free_ester',$this->is_show_free_ester);
		$criteria->compare('operator_note',$this->operator_note,true);
		$criteria->compare('phone_note',$this->phone_note,true);
		$criteria->compare('idle_city_distance',$this->idle_city_distance);
		$criteria->compare('idle_outside_city_distance',$this->idle_outside_city_distance);
		$criteria->compare('is_customer_chose',$this->is_customer_chose);
		$criteria->compare('is_pay_bonuses',$this->is_pay_bonuses);
		$criteria->compare('price_without_bonuses',$this->price_without_bonuses,true);
		$criteria->compare('income',$this->income,true);
		$criteria->compare('commission',$this->commission,true);
		$criteria->compare('is_use_commission',$this->is_use_commission);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Orders the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	protected function beforeDelete(){
        if(!parent::beforeDelete())
            return false;
		OrderService::model()->deleteAll('id_order = ?' , array($this->id));
		OrdersPoints::model()->deleteAll('id_order = ?' , array($this->id));
		OrderDriver::model()->deleteAll('id_order = ?' , array($this->id));
        return true;
    }
	
	private function PriceCalculation($count_point = 0, MapPoint $point = null)
	{
		$set = Settings::model()->findAll();
		$settings = array();
		foreach($set as $i => $s) {
			$settings[$s->param] = array('value'=>$s->value, 'type'=>$s->type);
		}
		
		$this->distance /= 1000;
			
		if($this->distance != 0) {
			$this->price_distance = $settings['price_kilometer']['value'] * $this->distance;
			$this->price = $this->price_distance;
			$this->price_without_class = $this->price_distance;
		}
		
		if($this->is_preliminary == 1) {
			if($settings['preliminary']['type'] == '1')
				$this->price += $settings['preliminary']['value'] * $this->price_distance / 100;
			else
				$this->price += $settings['preliminary']['value'];
		}	
		
		$prom_points_count = $count_point - 2;
		if(($prom_points_count) > 0) { 
			if($settings['intermediate_point']['type'] == '1') 
				$this->price += $this->price_distance * $prom_points_count * $settings['intermediate_point']['value'] / 100;
			else
				$this->price += $prom_points_count * $settings['intermediate_point']['value'];
		}
		
		$order_service = OrderService::model()->findAllByAttributes(array('id_order' => $this->id), array('order'=>'id ASC'));
	
		if(!empty($order_service)) {	
			foreach($order_service as $s) {
				if($s->service->is_percent) {
					$this->price += $this->price_distance * $s->service->value / 100;
				} else {
					$this->price += $s->service->value;
				}	
			}	
		}
		
		if($this->down_time != 0) {
			$this->price += $this->down_time * $settings['idle_price_minute']['value'];
		}
		
		if($this->idle_city_distance != 0) {
			$this->price += $this->idle_city_distance * $settings['idle_city_price_km']['value'];
		}
		
		if($this->idle_outside_city_distance != 0) {
			$this->price += $this->idle_outside_city_distance * $settings['idle_outside_city_price_km']['value'];
		}
		
		$this->price = OrderPrice::PriceTimeTariff($this->price, $this->order_date);
		
		if(!empty($point)) {
			$this->price += OrderPrice::PriceTariffZones($point->lat, $point->lng, $this->price_distance);
		}
		
		$this->price_without_class = round($this->price, 2);
		
		if($this->id_price_class != 1) {
			if($this->price_class->is_percent) {
				$this->price += $this->price_distance * $this->price_class->value / 100;
			} else {
				$this->price += $this->price_class->value;
			}	
		}
		
		if(!empty($this->id_customer)) {
			if($this->is_customer_chose) {
				$this->price = $this->PriceWithClass($this->customer);
			}
			
			$this->price = OrderPrice::getSale($this->price, $this->id_customer);
		}
		if(empty($this->id_parent) && $this->price < $settings['min_order_price']['value']) {
			$this->price = $settings['min_order_price']['value'];
		} 
		
		
		$this->price = round($this->price, 2);
		
		return $this;
	}
	
	
	
	public function recalculationOrder(Addresses $this_adress)
	{	
		
		$this->distance = 0;
		$this->price = 0;
		$this->price_distance = 0;
		$this->price_without_class = 0;
		
		$order_points = OrdersPoints::model()->findAllByAttributes(array('id_order' => $this->id), array('order'=>'id ASC'));
		
		$point = null;
		if(count($order_points) > 1) {
			$points = array();
			
			if(!empty($order_points)) {
				foreach($order_points as $i => $p) {
					if($i == 0 || ($i > 0 && $p->is_traversed == true))
						$points[] = $p->GetPointInfo();
				}
			}
			
			if(!empty($this_adress)) {							
				$points[] = $this_adress->getAttributes();
			}
			$this->distance = OrderPrice::calculationDistance($points);
			
			$point = new MapPoint($points[0]['latitude'], $points[0]['longitude']);
		} else {
			if(!empty($order->custom_route)) {
				$custom_route = explode("; ", $this->custom_route);
				
				if(!empty($custom_route)) {
					$route_points = array();
					foreach($custom_route as $p) {
						$route_points = explode(", ", $p);
					}
					
					for($i = 0; $i <= count($route_points) - 2; $i ++) {
						$this->distance += OrderPrice::getDistance($route_points[$i], $route_points[$i + 1]);
					}
					
				}
			}
		}
		
		$this->PriceCalculation(count($order_points), $point);
		
		
		return $this;
	}
	
	public function CompletionOrder(Addresses $this_adress, $flag = true ) {
		$this->recalculationOrder($this_adress);
		
		if($flag) {
			$id_status = 11;
		} else {
			$id_status = 10;
		}	
		
		$this->SetComplete($id_status);
		
		$this->termination_point = $this_adress->id;
		
		$this->ExecutionPayment();
		
		if(!$this->save()) {
			$errors = $this->GetErrors($new_order);
			echo json_encode(array('result' => 'failure', 'error' => $errors)); exit;
		} 
		
	}
	
	private function ExecutionPayment()
	{
		$customer = $this->customer;
		if(!empty($this->id_driver)) {	
			$driver = $this->driver;
			//$driver = Users::model()->findByPk($this->id_driver);
			//$driver->commission = (float)$driver->commission;
			if($this->is_use_commission) {
				$commission = $this->commission * $this->price / 100;
			} else {
				if($driver->commission == 0.00) {	
					$average_commission = Settings::model()->findByAttributes(array('param' =>'average_commission'));
					$commission = $average_commission->value * $this->price / 100;
				} else {
					if($driver->is_percent) {
						$commission = $driver->commission * $this->price / 100;
					} else {
						$commission = $driver->commission;
					}	
				}
			}
			$this->price_without_bonuses = $this->price;
			$this->bonuses = 0;
			
			if($this->is_pay_bonuses && $customer->bonuses > 0) {
				$sub = $customer->bonuses - $this->price;
				if($sub > 0) {
					$customer->bonuses = $sub;
					$this->price_without_bonuses = 0;
					$this->bonuses = $this->price;
					$driver->balance += $this->price;
				} else {
					$this->price_without_bonuses = -$sub;
					$driver->balance += $customer->bonuses;
					$this->bonuses = $customer->bonuses;
					$customer->bonuses = 0;
				}
				if($customer->save()) {
					BonusesHistory::RemoveBonuses($customer->id, $customer->bonuses, $this->bonuses, $this->id);
				}
				if($driver->save()) {
					PaymentsHistory::Depositing($driver->id, 7, $driver->balance, $driver->rating, $this->bonuses, $this->id );
				}
			}
			
			$commission = round($commission, 2);
			
			$this->income = $commission;
			$this->save();
			
			if($commission > 0) {
				$driver->balance -= $commission;
				
				if($driver->save()) {
					PaymentsHistory::RemoveOrderCommission($driver->id, $driver->balance, $driver->rating, $commission, $this->id );
				}
			}
			
		}
		return $this;
	}
	
	public function accrualBonusesOrder()
	{
		$customer = $this->customer;
		if($this->price > 0) {
			$bonuses_percentage_order = Settings::model()->findByAttributes(array('param' =>'bonuses_percentage_order'));
			$bonuses = $bonuses_percentage_order->value * $this->price / 100;
			$bonuses = round($bonuses, 2);
			
			$customer->bonuses += $bonuses;
			if($customer->save()) {
				BonusesHistory::Depositing($customer->id, $customer->bonuses, $bonuses, $this->id);
			}
		}
	}
	
	public function calculationOrder()
	{
		$this->distance = 0;
		$this->price = 0;
		$this->price_distance = 0;
		$this->price_without_class = 0;
		
		$order_points = OrdersPoints::model()->findAllByAttributes(array('id_order' => $this->id), array('order'=>'id ASC'));

		if(count($order_points) > 1) {
			$points = array();
			
			if(!empty($order_points)) {
				foreach($order_points as $i => $p) {
					$points[] = $p->GetPointInfo();
				}
			}
			
			$this->distance = OrderPrice::calculationDistance($points);
		} else {
			if(!empty($order->custom_route)) {
				$custom_route = explode("; ", $this->custom_route);
				
				if(!empty($custom_route)) {
					$route_points = array();
					foreach($custom_route as $p) {
						$route_points = explode(", ", $p);
					}
					
					for($i = 0; $i <= count($route_points) - 2; $i ++) {
						$this->distance += OrderPrice::getDistance($route_points[$i], $route_points[$i + 1]);
					}
					
				}
			}
		}
		
		$this->PriceCalculation(count($order_points), new MapPoint($points[0]['latitude'], $points[0]['longitude']));
		
		
		return $this;
	}
	
	public function CopyParentOrder(Orders $parent)
	{
		$this->attributes = $parent->attributes;
		$this->order_date = date('Y-m-d H:i:s');
		$this->change_date = date('Y-m-d H:i:s');
		$this->id_status = 5;
		$this->id_parent = $parent->id;
		$this->execution_status = 1;
		$this->is_show_free_ester = false;
		$this->id = NULL;
		$this->price = NULL;
		$this->distance = NULL;
		$this->price_distance = NULL;
		$this->price_without_class = NULL;
		$this->from = NULL;
		$this->where = NULL;
		
		return $this;
	}
	
	public function SummaryPrice()
	{
		if(!empty($this->id_parent)) {
			$parent_order = Orders::model()->findByPk($this->id_parent);
			if(!empty($parent_order)) {
				$this->price += $parent_order->price;
				$this->bonuses += $parent_order->bonuses;
				$this->price_without_bonuses += $parent_order->price_without_bonuses;
			}
		}
		return $this;
	}
	
	public function PriceWithClass(Users $driver)
	{
		$price = $this->price;
		if($this->id_price_class < $driver->id_price_class) {
			if(!empty($driver->price_class)) {
				$price = $this->price_without_class;
				if($driver->price_class->is_percent) {
					$price += $this->price_without_class * $driver->price_class->value / 100;
				} else {
					$price += $this->price_class->value;
				}
			}
		} 
		return $price;
	}
	
	public function SetCommission($commission)
	{
		$this->is_use_commission = true;
		$this->commission = $commission;
		$this->save();
		return $this;
	}
	
	public function getDriverNearby()
	{
		$criteria = new CDbCriteria();
		$criteria->mergeWith(array(
			'join'=>'INNER JOIN users driver ON driver.id = t.id_user',
			'condition'=>'driver.id_type = 1 AND driver.balance > 0',			
		));
		$address = $this->from_adress->adress;
		$criteria->addCondition('((DEGREES(ACOS((SIN(RADIANS('.$address->latitude.')) * SIN(RADIANS(cast(lat as numeric(8,2))))) + (COS(RADIANS('.$address->latitude.')) * COS(RADIANS(cast(lat as numeric(8,2)))) * COS(RADIANS('.$address->longitude.' - cast(lng as numeric(8,2)))))))) * 60 * 1.1515 * 1.609344) < 5');
		$criteria->addCondition("id_status = 1");
		$criteria->addCondition("moderation != 0 AND moderation != 2");
		$criteria->order = 'status_update ASC';
		$drivers = UserStatus::model()->findAll($criteria);
		
		$result = array();
		foreach($drivers as $key => $dr){
			if($dr->user->id_price_class == $this->id_price_class){
				$result[] = $dr;
				unset($drivers[$key]);
			}
		}
		$result = array_merge($result, $drivers);
		
		return $result;
	}
	
	public function SandRequestPerform($id_driver, $is_dispatcher_creator = false ) {
		$order_driver = OrderDriver::GetDriverRequest($this->id, $id_driver, $is_dispatcher_creator);
		
		if($order_driver->adopted != 0) {
			$order_driver->adopted = 0;
		}
		$order_driver->id_order = $this->id;
		$order_driver->id_driver = $id_driver;
		$order_driver->save();
	}

	public function SetToPhoneClient($id_driver, $is_dispatcher_creator = false ) {
		$order_driver = OrderDriver::GetDriverRequest($this->id, $id_driver, $is_dispatcher_creator);
		
		if($order_driver->adopted != 1) {
			$order_driver->adopted = 1;
		}
		$order_driver->id_order = $this->id;
		$order_driver->id_driver = $id_driver;
		$order_driver->save();
	}
	
	public function SetAvailable() {
		$this->execution_status = 1;
		$this->id_status = 1;
		$this->id_driver = NULL;
		$this->change_date = date('Y-m-d H:i:s');
		$this->save();
	}
	
	public function SetCancel($id_status) {
		$this->execution_status = 4;
		$this->id_status = $id_status;
		$this->change_date = date('Y-m-d H:i:s');
		$this->save();
	}
	
	public function SetComplete($id_status) {
		$this->execution_status = 3;
		$this->id_status = $id_status;
		$this->is_customer_chose = false;
		$this->change_date = date('Y-m-d H:i:s');
		$this->save();
	}
	
	public function ChangeExecuting($id_status, $driver_id, $is_customer_chose = false ) {
		$this->id_status = $id_status;
		$this->execution_status = 2;
		if($is_customer_chose) {
			$this->is_customer_chose = true;
		}
		$this->id_driver = $driver_id;
		$this->change_date = date('Y-m-d H:i:s');
		$this->save();
	}
	
	public function GetOrderInfo()
	{	
		$result = $this->getAttributes();
		$result['driver'] = '';
		$result['car'] =  '';
		$result['where'] = '';
		if(!empty($this->from))
			$result['from'] = $this->from_adress->adress->name;
		/*	
		if(!empty($this->where))
			$result['where'] = $this->where_adress->adress->name;	
		*/
		$order_points = OrdersPoints::model()->findAllByAttributes(array('id_order' => $this->id), array('order'=>'id ASC'));
		
		$result['where'] = array();
		if(count($order_points) > 1) {
			foreach($order_points as $j => $p) {
				if($j > 0) {
					$result['where'][] = $p->adress->name;
				}
			}
		}
		
		if(!empty($this->id_driver)) {
			$result['driver'] = $this->driver->name.' '.$this->driver->surname;
			if(!empty($this->driver->id_car)) {
				$result['car'] = $this->driver->car->marka.' '.$this->driver->car->model;
			}	
		} 
		return $result;
	}
	
	public function GetOrderAdvancedInfo($driver_id = null)
	{	
		$result = $this->attributes;
		/*
		if($this->is_pay_bonuses && !empty($this->customer)) {
			$result['bonuses'] = $this->customer->bonuses;
		}
		*/
		if(!empty($this->from_adress)) {
			$result['from'] = $this->from_adress->adress->name.' '.$this->from_adress->entrance;
			$result['from_latitude'] = $this->from_adress->adress->latitude;
			$result['from_longitude'] = $this->from_adress->adress->longitude;
		}
		$order_points = OrdersPoints::model()->findAllByAttributes(array('id_order' => $this->id), array('order'=>'id ASC'));
		
		$result['where'] = array();
		if(count($order_points) > 1) {
			foreach($order_points as $j => $p) {
				if($j > 0) {
					$result['where'][] = $p->adress->name.' '.$p->entrance;
				}
			}
		}
		$result['price_class'] = $this->price_class->name;	
		if(!empty($this->services)) {
			foreach($this->services as $serv) {
				$result['Services'][] = $serv->service->name;
			}
		}
		
		if(!empty($driver_id)) {
			$driver = Users::model()->findByPk($driver_id);
			
			$result['price'] = $this->PriceWithClass($driver);
		}
		$customer = Users::model()->findByPk($this->id_customer);
		$result['bonuses'] = OrderPrice::BobusesInfo($result['is_pay_bonuses'], $customer->bonuses, $result['price']);

		return $result;
	}
	
	public function ChangeStatus($id_status) {
		if($this->id_status != $id_status) {
			$this->id_status = $id_status;
			$this->save();
		}	
	}
}
