<?php

/**
 * This is the model class for table "users".
 *
 * The followings are the available columns in table 'users':
 * @property integer $id
 * @property integer $type
 * @property integer $phone
 * @property string $name
 * @property string $surname
 * @property string $patronymic
 * @property string $email
 * @property string $nickname
 * @property integer $status
 * @property string $balance
 * @property string $photo
 */
class Users extends CActiveRecord
{	
	public $directory_name = 'users';
	public $images;
	public $image_old;
	public $is_delete = false;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'users';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type, phone, name, surname, patronymic, email, nickname, status, balance', 'required'),
			array('type, phone, status', 'numerical', 'integerOnly'=>true),
			array('name, surname, patronymic, email, nickname, photo, password', 'length', 'max'=>255),
			array('balance', 'length', 'max'=>8),
			array('email', 'email'),
			array('photo','file','types'=>'jpg, gif, png', 'allowEmpty'=>true,'on'=>'insert,update'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, type, phone, name, surname, patronymic, email, nickname, status, balance, photo', 'safe', 'on'=>'search'),
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
			'user_type' => array(self::BELONGS_TO, 'UserTypes', 'type'),
			'user_car' => array(self::BELONGS_TO, 'Cars', 'car'),
			'user_status' => array(self::BELONGS_TO, 'Statuses', 'status'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'type' => 'Type',
			'phone' => 'Phone',
			'name' => 'Name',
			'surname' => 'Surname',
			'patronymic' => 'Patronymic',
			'email' => 'Email',
			'nickname' => 'Nickname',
			'status' => 'Status',
			'balance' => 'Balance',
			'photo' => 'Photo',
			'car' => 'Car',
			'password' => 'password',
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
		$criteria->compare('type',$this->type);
		$criteria->compare('phone',$this->phone);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('surname',$this->surname,true);
		$criteria->compare('patronymic',$this->patronymic,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('nickname',$this->nickname,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('balance',$this->balance,true);
		$criteria->compare('photo',$this->photo,true);
		$criteria->compare('car',$this->car);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Users the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function validatePassword($password)
    {
        return CPasswordHelper::verifyPassword($password,$this->password);
    }
 
    public function hashPassword($password)
    {
        return CPasswordHelper::hashPassword($password);
    }
	
	protected function beforeSave(){
        if(!parent::beforeSave())
            return false;
        if(($this->scenario=='update') && ($image=CUploadedFile::getInstance($this,'photo'))){
			$this->deleteDocument(); // старый документ удалим, потому что загружаем новый
            $this->images=$image;
			@mkdir(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name);
            $this->images->saveAs(
                Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->id.'.'.$image->getExtensionName());
			$this->photo = $image->getExtensionName();	
		}
        return true;
    }
	
	public function afterSave(){
       // if(!parent::beforeSave())
        //    return false;
		//print_r($_POST); exit;
        if(($this->scenario=='insert') && ($image=CUploadedFile::getInstance($this,'photo'))){
			//$this->deleteDocument(); // старый документ удалим, потому что загружаем новый
            $this->images = $image;
			@mkdir(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name);
            $this->images->saveAs(
                Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->id.'.'.$image->getExtensionName());
			$this->photo = $image->getExtensionName();
		}
        return true;
    }
 
    protected function beforeDelete(){
        if(!parent::beforeDelete())
            return false;
		$this->is_delete = true;
        $this->deleteDocument(); // удалили модель? удаляем и файл
        return true;
    }
 
    public function deleteDocument(){
		if($this->is_delete)
			$documentPath=Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->id.'.'.$this->photo;
		else
			$documentPath=Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->id.'.'.$this->image_old;
		if(is_file($documentPath)) {
            unlink($documentPath);
			$this->is_delete = false;
		}	
		
    }
}
