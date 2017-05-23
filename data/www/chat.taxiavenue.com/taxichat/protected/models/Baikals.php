<?php

/**
 * This is the model class for table "baikals".
 *
 * The followings are the available columns in table 'baikals':
 * @property integer $id
 * @property integer $id_driver
 * @property integer $status
 * @property integer $actual
 * @property integer $dispatcher_view
 * @property string $message
 */
class Baikals extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'baikals';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_driver, status, actual', 'required'),
			array('id_driver, status, actual, dispatcher_view', 'numerical', 'integerOnly'=>true),
			array('message', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, id_driver, status, actual, dispatcher_view, message', 'safe', 'on'=>'search'),
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
			'driver' => array(self::BELONGS_TO, 'Users', 'id_driver'),
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
			'status' => 'Status',
			'actual' => 'Actual',
			'dispatcher_view' => 'Dispatcher View',
			'message' => 'Message',
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
		$criteria->compare('status',$this->status);
		$criteria->compare('actual',$this->actual);
		$criteria->compare('dispatcher_view',$this->dispatcher_view);
		$criteria->compare('message',$this->message,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Baikals the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
