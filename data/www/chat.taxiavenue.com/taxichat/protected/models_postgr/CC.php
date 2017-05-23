<?php

/**
 * This is the model class for table "users_temp".
 *
 * The followings are the available columns in table 'users_temp':
 * @property integer $id
 * @property integer $id_type
 * @property string $phone
 * @property string $name
 * @property string $surname
 * @property string $email
 * @property string $nickname
 * @property string $balance
 * @property string $photo
 * @property integer $id_car
 * @property string $password
 * @property string $rating
 * @property string $dop_info
 * @property string $commission
 * @property boolean $is_percent
 * @property integer $id_price_class
 * @property integer $id_driver
 * @property string $bonuses
 */
class UsersTemp extends CActiveRecord
{
	public $directory_name = 'users_temp';
	public $images;
	private $nickname_translit;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'users_temp';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_type, phone, nickname, password', 'required'),
			array('id_type, id_car, id_price_class, id_driver', 'numerical', 'integerOnly'=>true),
			array('nickname', 'unique'),
			//array('phone', 'length', 'max'=>15),
			array('phone', 'phone_validate'),
			array('nickname', 'nickname_validate'),
			array('email', 'email'),
			array('name, surname, email, nickname, photo, password', 'length', 'max'=>255),
			array('balance, commission, bonuses', 'length', 'max'=>8),
			array('rating', 'length', 'max'=>4),
			array('dop_info, is_percent', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, id_type, phone, name, surname, email, nickname, balance, photo, id_car, password, rating, dop_info, commission, is_percent, id_price_class, id_driver, bonuses', 'safe', 'on'=>'search'),
		);
	}
	
	public function phone_validate($attribute)
	{
		$pattern = '/^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/';  
		if(!preg_match($pattern, $this->$attribute)) {
		  $this->addError($attribute, 'Неверный формат телефона.');
		}
		if($this->id_type <= 2) {
			$user = Users::model()->findByAttributes(array('phone' => $this->$attribute, 'id_type' => $this->id_type));
		} else {
			$user = Users::model()->findByAttributes(array('phone' => $this->$attribute));
		}	
		if(!empty($user) && $user->id != $this->id) {
			$this->addError($attribute, 'Пользователь с таким номером телефона уже зарегистрирован.');
		}
	}
	
	public function nickname_validate($attribute)
	{	
		$this->$attribute = trim($this->$attribute);

		if(strpos($this->$attribute, 0x20) !== false)
			$this->addError($attribute, 'Ник не должен содержать пробелы.');
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'type' => array(self::BELONGS_TO, 'UserTypes', 'id_type'),
			'car' => array(self::BELONGS_TO, 'Cars', 'id_car'),
			'price_class' => array(self::BELONGS_TO, 'PriceClass', 'id_price_class'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'id_type' => 'Тип пользователя',
			'phone' => 'Телефон',
			'name' => 'Имя',
			'surname' => 'Фамилия',
			'email' => 'Email',
			'nickname' => 'Ник',
			'balance' => 'Баланс',
			'photo' => 'Фото',
			'id_car' => 'Id Car',
			'password' => 'Пароль',
			'rating' => 'Рейтинг',
			'dop_info' => 'Дополнительная информация',
			'commission' => 'Размер комиссии с заказа',
			'is_percent' => 'Тип комиссии с заказа',
			'id_price_class' => 'Ценовой класс',
			'id_driver' => 'Id Driver',
			'bonuses' => 'Бонусы',
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
		$criteria->compare('id_type',$this->id_type);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('surname',$this->surname,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('nickname',$this->nickname,true);
		$criteria->compare('balance',$this->balance,true);
		$criteria->compare('photo',$this->photo,true);
		$criteria->compare('id_car',$this->id_car);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('rating',$this->rating,true);
		$criteria->compare('dop_info',$this->dop_info,true);
		$criteria->compare('commission',$this->commission,true);
		$criteria->compare('is_percent',$this->is_percent);
		$criteria->compare('id_price_class',$this->id_price_class);
		$criteria->compare('id_driver',$this->id_driver);
		$criteria->compare('bonuses',$this->bonuses,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UsersTemp the static model class
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
		
		$user = Users::model()->findByPk($this->id_driver);
		if(empty($user)) {
			$this->password = crypt($this->password);
		} elseif(!empty($this->password) && $user->password != $this->password) {
			 $this->password = crypt($this->password);
		} 
		
		$this->nickname_translit = preg_replace("/[^0-9A-zА-я -]+/u", "", $this->_translit($this->nickname));
		
        if($image=CUploadedFile::getInstance($this,'photo')){
			if(!empty($user)) {
				$this->deleteFile(false, $user->photo); // старый файл удалим, потому что загружаем новый
			}
            $this->images=$image;
			@mkdir(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name);
            
			$file_name = $this->nickname_translit.'_'.time();
			$this->images->saveAs(
                Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$file_name.'.'.$image->getExtensionName());
			$this->photo = $file_name.'.'.$image->getExtensionName();	
		}
        return true;
    }
 
    protected function beforeDelete(){
        if(!parent::beforeDelete())
            return false;
        $this->deleteFile(true); // удалили модель? удаляем и файл
        return true;
    }
 
    public function deleteFile($is_delete = false, $photo = null){
		if($is_delete) {
			$documentPath = Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->photo;
                }
		else {
			$documentPath=Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$photo;
		}
		if(is_file($documentPath)) {
            unlink($documentPath);
		}	
		
    }
	
	public function copy_photo(){
		if(is_file(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.'users'.DIRECTORY_SEPARATOR.$this->photo))
			copy(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.'users'.DIRECTORY_SEPARATOR.$this->photo, Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->photo);
    }
	
	protected function _translit($string){
		  $table = array( 
                'А' => 'A', 
                'Б' => 'B', 
                'В' => 'V', 
                'Г' => 'G', 
                'Д' => 'D', 
                'Е' => 'E', 
                'Ё' => 'YO', 
                'Ж' => 'ZH', 
                'З' => 'Z', 
                'И' => 'I', 
                'Й' => 'J', 
                'К' => 'K', 
                'Л' => 'L', 
                'М' => 'M', 
                'Н' => 'N', 
                'О' => 'O', 
                'П' => 'P', 
                'Р' => 'R', 
                'С' => 'S', 
                'Т' => 'T', 
                'У' => 'U', 
                'Ф' => 'F', 
                'Х' => 'H', 
                'Ц' => 'C', 
                'Ч' => 'CH', 
                'Ш' => 'SH', 
                'Щ' => 'CSH', 
                'Ь' => '', 
                'Ы' => 'Y', 
                'Ъ' => '', 
                'Э' => 'E', 
                'Ю' => 'YU', 
                'Я' => 'YA', 

                'а' => 'a', 
                'б' => 'b', 
                'в' => 'v', 
                'г' => 'g', 
                'д' => 'd', 
                'е' => 'e', 
                'ё' => 'yo', 
                'ж' => 'zh', 
                'з' => 'z', 
                'и' => 'i', 
                'й' => 'j', 
                'к' => 'k', 
                'л' => 'l', 
                'м' => 'm', 
                'н' => 'n', 
                'о' => 'o', 
                'п' => 'p', 
                'р' => 'r', 
                'с' => 's', 
                'т' => 't', 
                'у' => 'u', 
                'ф' => 'f', 
                'х' => 'h', 
                'ц' => 'c', 
                'ч' => 'ch', 
                'ш' => 'sh', 
                'щ' => 'csh', 
                'ь' => '', 
                'ы' => 'y', 
                'ъ' => '', 
                'э' => 'e', 
                'ю' => 'yu', 
                'я' => 'ya',
				'\\' => '_',
				' ' => '_'				
		); 

		$output = str_replace( 
			array_keys($table), 
			array_values($table),$string 
		); 

		return $output;
	}
	
	public function SetProperties($properties = null)
	{	
		if(!empty($properties)) {
			
			foreach($properties as $i => $element) {
				if($i == 'photo') {
					if($element == null) {
						unset($properties[$i]);
					}		
				} 
			}
			$this->attributes = $properties;
		}
		return $this;
	}
}
