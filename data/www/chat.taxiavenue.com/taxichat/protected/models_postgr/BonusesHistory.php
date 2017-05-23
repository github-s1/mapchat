<?php

/**
 * This is the model class for table "bonuses_history".
 *
 * The followings are the available columns in table 'bonuses_history':
 * @property integer $id
 * @property integer $id_user
 * @property integer $id_type
 * @property string $value
 * @property string $bonuses
 * @property integer $id_order
 * @property string $date_create
 */
class BonusesHistory extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'bonuses_history';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_user, id_type, value, bonuses, id_order', 'required'),
			array('id_user, id_type, id_order', 'numerical', 'integerOnly'=>true),
			array('value, bonuses', 'length', 'max'=>8),
			array('date_create', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, id_user, id_type, value, bonuses, id_order, date_create', 'safe', 'on'=>'search'),
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
			'type' => array(self::BELONGS_TO, 'OperationsBonuses', 'id_type'),
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
			'value' => 'Value',
			'bonuses' => 'Bonuses',
			'id_order' => 'Id Order',
			'date_create' => 'Date Create',
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
		$criteria->compare('bonuses',$this->bonuses,true);
		$criteria->compare('id_order',$this->id_order);
		$criteria->compare('date_create',$this->date_create,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return BonusesHistory the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function GetBonusInfo()
	{
		$result = $this->getAttributes();
		if(!empty($this->type)) {
			$result['type'] = $this->type->name;
		}
		return $result;
	}
	
	public static function GetBonusesInfo($bonuses_history = null)
	{	
		$b_history = array();
		$start_date = NULL;
		$end_date = NULL;
		if(!empty($bonuses_history)) {
			foreach($bonuses_history as $i => $p) {
				$b_history[$i] = $p->GetBonusInfo();
			}
			$start_date = $b_history[count($b_history) - 1]['date_create'];
			$end_date = $b_history[0]['date_create'];
		}
		
		return array('bonuses_history' => $b_history, 'start_date' => $start_date, 'end_date' => $end_date);	
	}
	
	public static function BonusesOperation($id_user, $id_type, $bonuses, $value, $id_order)
	{
		$new_fine = new BonusesHistory;
		$new_fine->id_user = $id_user;
		$new_fine->id_type = $id_type;
		$new_fine->bonuses = $bonuses;
		$new_fine->value = $value;
		$new_fine->id_order = $id_order;
		$new_fine->date_create = date('Y-m-d H:i:s');
		$new_fine->save();
		
		return $new_fine;
	}
	
	public static function Depositing($id_user, $bonuses, $value, $id_order)
	{
		$new_fine = self::BonusesOperation($id_user, 1, $bonuses, $value, $id_order);
		
		return $new_fine;
	}
	
	public static function RemoveBonuses($id_user, $bonuses, $value, $id_order)
	{
		$new_fine = self::BonusesOperation($id_user, 2, $bonuses, -$value, $id_order);
		
		return $new_fine;
	}
}
