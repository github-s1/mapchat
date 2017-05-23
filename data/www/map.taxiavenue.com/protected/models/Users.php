<?php

/**
 * This is the model class for table "users".
 *
 * The followings are the available columns in table 'users':
 * @property integer $id
 * @property integer $id_avatar
 * @property string $login
 * @property string $pass
 * @property string $name
 * @property string $family
 * @property string $sex
 * @property integer $age
 * @property string $about
 * @property string $date_register
 * @property string $soc_register
 * @property string $telephone
 * @property string $email
 * @property string $city
 * @property integer $online
 * @property string $active
 * @property string $confirm_date
 * @property string $confirm_code
 * @property integer $anonymous
 * @property string $status
 *
 * The followings are the available model relations:
 * @property Comments[] $comments
 * @property Interests[] $interests
 * @property Kind[] $kinds
 * @property Mark[] $marks
 * @property StatusUser[] $statusUsers
 * @property Avatar $idAvatar
 */
class Users extends CActiveRecord
{
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
			array('login, pass', 'required'),
			//array('login', 'unique', 'except' => 'openAuth, update, register'),
            array('login', 'uniqueLogin'),
			array('id_avatar, age, online, anonymous', 'numerical', 'integerOnly'=>true),
			array('login, soc_register, telephone, email', 'length', 'max'=>100),
			array('pass', 'length', 'max'=>255),
			array('name', 'length', 'max'=>100),
			array('family', 'length', 'max'=>155),
			array('sex', 'length', 'max'=>7),
			array('about', 'length', 'max'=>500),
			array('city', 'length', 'max'=>80),
			array('active', 'length', 'max'=>1),
			array('confirm_date', 'length', 'max'=>20),
			array('confirm_code, status', 'length', 'max'=>50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, id_avatar, login, pass, name, family, sex, age, about, date_register, soc_register, telephone, email, city, online, active, confirm_date, confirm_code, anonymous, status', 'safe', 'on'=>'search'),
		);
	}

    /**
     * Проверка логина на уникальность при разных сценариях
     */
    public function uniqueLogin($attribute) {
        $scenario = $this->getScenario();
        if ($scenario == 'openAuth' or $scenario == 'update') return true; // уникальность не нужна
        if ($scenario == 'register') {
            $res = $this->findByAttributes(array('login' => $this->login, 'soc_register' => null));
            if (!$res) return true;
        }
        $this->addError($attribute, 'логин занят');
    }

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'comments' => array(self::HAS_MANY, 'Comments', 'id_user'),
			'interests' => array(self::HAS_MANY, 'Interests', 'id_user'),
			'marks' => array(self::HAS_MANY, 'Mark', 'id_user'),
			'messages' => array(self::HAS_MANY, 'Messages', 'id_user_ot'),
			'sessions' => array(self::HAS_MANY, 'Session', 'id_user'),
			'idAvatar' => array(self::BELONGS_TO, 'Avatar', 'id_avatar'),
			'CityInfo' => array(self::BELONGS_TO, 'City', 'city'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'id_avatar' => 'Id Avatar',
			'login' => 'Login',
			'pass' => 'Pass',
			'name' => 'Name',
			'family' => 'Family',
			'sex' => 'Sex',
			'age' => 'Age',
			'about' => 'About',
			'date_register' => 'Date Register',
			'soc_register' => 'Soc Register',
			'telephone' => 'Telephone',
			'email' => 'Email',
			'city' => 'City',
			'online' => 'Online',
			'active' => 'Active',
			'confirm_date' => 'Confirm Date',
			'confirm_code' => 'Confirm Code',
			'anonymous' => 'Anonymous',
			'status' => 'Status',
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
		$criteria->compare('id_avatar',$this->id_avatar);
		$criteria->compare('login',$this->login,true);
		$criteria->compare('pass',$this->pass,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('family',$this->family,true);
		$criteria->compare('sex',$this->sex,true);
		$criteria->compare('age',$this->age);
		$criteria->compare('about',$this->about,true);
		$criteria->compare('date_register',$this->date_register,true);
		$criteria->compare('soc_register',$this->soc_register,true);
		$criteria->compare('telephone',$this->telephone,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('online',$this->online);
		$criteria->compare('active',$this->active,true);
		$criteria->compare('confirm_date',$this->confirm_date,true);
		$criteria->compare('confirm_code',$this->confirm_code,true);
		$criteria->compare('anonymous',$this->anonymous);
		$criteria->compare('status',$this->status,true);

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
	
	public function SetParams($attr = null)
	{	
		if(!empty($attr)) {
			foreach($attr as $i => $element) {
				if(empty($element)) {
					unset($attr[$i]);
				}
			}

			if(isset($attr['anonymous'])) {
				if ($attr['anonymous'] != 0 && $attr['anonymous'] != 1){  
					unset($attr['anonymous']); 
				}
			} else {
				unset($attr['anonymous']); 
			}
			
			if(isset($attr['active'])) {
				if ($attr['active'] != 'y' && $attr['active'] != 'n'){  
					unset($attr['active']); 
				}
			} else {
				unset($attr['active']); 
			}
			
			$this->attributes = $attr;
			
			if (isset($attr['status'])){
                self::updateStatus($this->id, $attr['status']);
            }
		}	
		return $this;
	}
	
	public static function updateStatus($id_user, $status){
		if(!empty($id_user)) {
			$StatusUser = StatusUser::model()->findByAttributes(array('id_user' => $id_user));
			if(empty($StatusUser)) {
				$StatusUser = new StatusUser;
				$StatusUser->id_user = $id_user;
			}
			$StatusUser->status = $status;
			$StatusUser->createDatatime = date('Y-m-d H:i:s');
			
			$StatusUser->save();
		}	
    }
    
    public function getAttributes($names = true, $isMobile = false) {
        $attr = parent::getAttributes($names);
        if ($isMobile == false) return $attr;
        
        // Для мобильного удаляем некоторые поля
        unset($attr['id_avatar']);
        unset($attr['pass']);
        
        // Для мобильного дополняем данные
        $avatar = $this->idAvatar->getAttributes();
        $attr['url_big'] = Yii::app()->getBaseUrl(true) . '/img/users_avatar/' . $avatar['big_photo'];
        $attr['url_small'] = Yii::app()->getBaseUrl(true) . '/img/users_avatar/small/' . $avatar['small_photo'];

        return $attr;
    }
}