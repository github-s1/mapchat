<?php

/**
 * This is the model class for table "mark".
 *
 * The followings are the available columns in table 'mark':
 * @property integer $id
 * @property integer $id_kind
 * @property integer $id_user
 * @property string $description
 * @property string $address
 * @property string $createDatatime
 * @property string $active
 * @property string $anonymous
 * @property integer $click_spam
 * @property integer $views
 * @property string $active_balloon
 * @property integer $period
 *
 * The followings are the available model relations:
 * @property Audio[] $audios
 * @property Comments[] $comments
 * @property Kind $idKind
 * @property Users $idUser
 * @property Photo[] $photos
 * @property Point[] $points
 */
class Mark extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'mark';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_kind, id_user, createDatatime', 'required'),
			array('id_kind, id_user, click_spam, views, period', 'numerical', 'integerOnly'=>true),
			//array('description', 'length', 'max'=>5000),
			array('address', 'length', 'max'=>255),
			array('active, anonymous, active_balloon', 'length', 'max'=>1),
			array('description', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, id_kind, id_user, description, address, createDatatime, active, anonymous, click_spam, views, active_balloon, period', 'safe', 'on'=>'search'),
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
			'audios' => array(self::HAS_MANY, 'Audio', 'id_mark'),
			'comments' => array(self::HAS_MANY, 'Comments', 'id_mark'),
			'idKind' => array(self::BELONGS_TO, 'Kind', 'id_kind'),
			'idUser' => array(self::BELONGS_TO, 'Users', 'id_user'),
			'photos' => array(self::HAS_MANY, 'Photo', 'id_mark'),
			'points' => array(self::HAS_MANY, 'Point', 'id_mark'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'id_kind' => 'Id Kind',
			'id_user' => 'Id User',
			'description' => 'Description',
			'address' => 'Address',
			'createDatatime' => 'Create Datatime',
			'active' => 'Active',
			'anonymous' => 'Anonymous',
			'click_spam' => 'Click Spam',
			'views' => 'Views',
			'active_balloon' => 'Active Balloon',
			'period' => 'Period',
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
		$criteria->compare('id_kind',$this->id_kind);
		$criteria->compare('id_user',$this->id_user);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('createDatatime',$this->createDatatime,true);
		$criteria->compare('active',$this->active,true);
		$criteria->compare('anonymous',$this->anonymous,true);
		$criteria->compare('click_spam',$this->click_spam);
		$criteria->compare('views',$this->views);
		$criteria->compare('active_balloon',$this->active_balloon,true);
		$criteria->compare('period',$this->period);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Mark the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}	
	public function ViewsIncrement() 
	{
		$this->views += 1;
		$this->save();	
	}
	
}
