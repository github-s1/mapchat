<?php

/**
 * This is the model class for table "driver_reviews".
 *
 * The followings are the available columns in table 'driver_reviews':
 * @property integer $id
 * @property integer $id_driver
 * @property integer $id_customer
 * @property integer $id_evaluation
 * @property string $text
 * @property string $date_review
 * @property string $rating
 * @property integer $id_order
 */
class DriverReviews extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'driver_reviews';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_driver, id_customer, id_evaluation, date_review, rating, id_order', 'required'),
			array('id_driver, id_customer, id_evaluation, id_order', 'numerical', 'integerOnly'=>true),
			array('rating', 'length', 'max'=>4),
			array('text', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, id_driver, id_customer, id_evaluation, text, date_review, rating, id_order', 'safe', 'on'=>'search'),
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
			'customer' => array(self::BELONGS_TO, 'Users', 'id_customer'),
			'evaluation' => array(self::BELONGS_TO, 'Evaluations', 'id_evaluation'),
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
			'id_customer' => 'Id Customer',
			'id_evaluation' => 'Id Evaluation',
			'text' => 'Text',
			'date_review' => 'Date Review',
			'rating' => 'Rating',
			'id_order' => 'Id Order',
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
		$criteria->compare('id_customer',$this->id_customer);
		$criteria->compare('id_evaluation',$this->id_evaluation);
		$criteria->compare('text',$this->text,true);
		$criteria->compare('date_review',$this->date_review,true);
		$criteria->compare('rating',$this->rating,true);
		$criteria->compare('id_order',$this->id_order);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return DriverReviews the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function GetReviewInfo()
	{
		$result = $this->getAttributes();
		if(!empty($this->customer)) {
			$result['customer'] = $this->customer->nickname;
		}
		if(!empty($this->driver)) {
			$result['driver'] = $this->driver->name.' '.$this->driver->surname;
		}
		if(!empty($this->evaluation)) {
			$result['evaluation'] = $this->evaluation->name;
		}
		return $result;
	}
	
	public static function GetReviewsInfo($driver_reviews = null)
	{	
		$reviews = array();
		$start_date = NULL;
		$end_date = NULL;
		if(!empty($driver_reviews)) {
			foreach($driver_reviews as $i => $r) {
				$reviews[$i] = $r->GetReviewInfo();
			}
			$start_date = $reviews[count($reviews) - 1]['date_review'];
			$end_date = $reviews[0]['date_review'];
		}
		
		return array('driver_reviews' => $reviews, 'start_date' => $start_date, 'end_date' => $end_date);
	}
}
