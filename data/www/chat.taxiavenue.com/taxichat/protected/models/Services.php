<?php

/**
 * This is the model class for table "services".
 *
 * The followings are the available columns in table 'services':
 * @property integer $id
 * @property string $name
 * @property integer $is_percent
 * @property string $value
 * @property integer $is_driver
 */
class Services extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'services';
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
			array('is_percent, is_driver', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			array('value', 'length', 'max'=>8),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, is_percent, value, is_driver', 'safe', 'on'=>'search'),
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
			'name' => 'Name',
			'is_percent' => 'Is Percent',
			'value' => 'Value',
			'is_driver' => 'Is Driver',
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
		$criteria->compare('is_driver',$this->is_driver);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Services the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public static function GetAll($is_driver = 0, $is_mobile = false) {
		$services_all = Services::model()->findAllByAttributes(array('is_driver' => $is_driver), array('order'=>'id ASC'));
		
		if($is_mobile) {
			$serv_name_all = array();
			$serv_index_all = array();
			if(!empty($services_all)) {
				foreach($services_all as $serv) {
					$serv_name_all[] = $serv->name;
					$serv_index_all[] = $serv->id;
				}
				
			}
			return array('names' => $serv_name_all, 'indexes' => $serv_index_all);
		} else {
			$services_all = CHtml::listData($services_all, 'id', 'name');
			
			return $services_all;
		}
		
	}
}
