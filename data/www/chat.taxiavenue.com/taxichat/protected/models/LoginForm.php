<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class LoginForm extends CFormModel
{
	public $username;
	public $password;
	public $rememberMe;

	private $_identity;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('username, password', 'required'),
			// rememberMe needs to be a boolean
			array('rememberMe', 'boolean'),
			// password needs to be authenticated
			array('password', 'authenticate'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'rememberMe'=>'Запонить меня',
			'username'=>'Логин',
			'password'=>'Пароль',
		);
	}

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute,$params) {
		if(!$this->hasErrors())
		{
			$this->_identity = new UserIdentity($this->username,$this->password);
			if(!$this->_identity->authenticate())
				$this->addError('password','Incorrect username or password.');
		}
	}
	
	//логинит пользователя возвращает true либо false взависимости от того прошла ли авторизация
	//$type - тип пользователя
	public function login($type = 3) {	
		
		$this->_identity = new UserIdentity($this->username,$this->password);
		
		if($this->_identity->authenticate($type)) {
			
			if($type <= 2) {
				$duration = 3600 * 24 * 30;
			} else {
				$duration = 3600 * 1;
			}
			if(Yii::app()->user->login($this->_identity, $duration)) {
				$user = Users::model()->findByPk(Yii::app()->user->id);
						
				$user->setStatusOnline();	
			
				return true;
			}
		}	
		return false;
	}
	//автоизирует пользователя возвращает true либо false взависимости от того прошла ли авторизация
	//$type - тип пользователя
	public static function UserAuthorize($phone, $password, $type) {	
		$model = new LoginForm;
		$model->attributes = array('username'=>$phone, 'password'=>$password);
		$model->rememberMe = 1;	
		
		return $model->login($type);
	}
	
}
