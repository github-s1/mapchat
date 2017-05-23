<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
    /**
     * Authenticates a user.
     * The example implementation makes sure if the username and password
     * are both 'demo'.
     * In practical applications, this should be changed to authenticate
     * against some persistent user identity storage (e.g. database).
     * @return boolean whether authentication succeeds.

    /*создаём приватное поле
        в котором будем хранить id*/
    private $_id;
    
    /**
     * Логинится ли пользователь через соцсети
     * если да - то пароль не проверяем
     */
    //private $_isOpenAuth;
    
    /*public function __construct($username, $password, $isOpenAuth = false) {
        parent::__construct($username, $password);
        $this->_isOpenAuth = $isOpenAuth;
    }*/


    public function authenticate()
    {
        $record=Users::model()->findByAttributes(array('login'=>$this->username, 'active'=>'y', 'soc_register' => null));
		if($record===null){
            $this->errorCode=self::ERROR_USERNAME_INVALID;
        }
        else {
            if($record->pass == crypt($this->password,$record->pass)) {
                $this->_id=$record->id;
                $record->online=1;//устанавливаем флаг онлайна пользователя
                $record->save();
                $this->setState('user_id', $record->id);
                
                $name = $record->name;
                if (!$name) $name = $record->login;
                $this->setState('first_name', $name);
                $this->setState('last_name', $record->family);
                
                $this->errorCode=self::ERROR_NONE;
			} else  {
                $this->errorCode=self::ERROR_PASSWORD_INVALID;
            }
        }
        return !$this->errorCode;
    }

    public function authenticate_social($uid, $system) {
        $record=Users::model()->findByAttributes(array('login'=>$this->username, 'active'=>'y', 'soc_id' => $uid, 'soc_register' => $system));
		if($record===null){
            $this->errorCode=self::ERROR_USERNAME_INVALID;
        }
        else {
            //if($record->pass == crypt($this->password,$record->pass)) {
                $this->_id=$record->id;
                $record->online=1;//устанавливаем флаг онлайна пользователя
                $record->save();
                $this->setState('user_id', $record->id);
                
                $name = $record->name;
                if (!$name) $name = $record->login;
                $this->setState('first_name', $name);
                $this->setState('last_name', $record->family);
                
                $this->errorCode=self::ERROR_NONE;
			/*} else  {
                $this->errorCode=self::ERROR_PASSWORD_INVALID;
            }*/
        }
        return !$this->errorCode;
    }
    
	public function authenticate_register()
    {
        $record=Users::model()->findByAttributes(array('login'=>$this->username, 'active'=>'y', 'soc_register' => null));
       
		if($record===null){
            $this->errorCode=self::ERROR_USERNAME_INVALID;
        }
        else {
            if($record->pass == crypt($this->password,$record->pass) || $record->pass == $this->password) {
                $this->_id=$record->id;
                $record->online=1;//устанавливаем флаг онлайна пользователя
                $record->save();
                $this->setState('user_id', $record->id);
                $name = $record->name;
                if (!$name) $name = $record->login;
                $this->setState('first_name', $name);
                $this->setState('last_name', $record->family);
                $this->errorCode=self::ERROR_NONE;
			} else  {
                $this->errorCode=self::ERROR_PASSWORD_INVALID;
            }
        }
        return !$this->errorCode;
    }

    public function authenticate_with_crypt()
    {
        $record=Users::model()->findByAttributes(array('login'=>$this->username));
        if($record===null){
            $this->errorCode=self::ERROR_USERNAME_INVALID;
        }
        else {
            if($record->pass!==$this->password)
                $this->errorCode=self::ERROR_PASSWORD_INVALID;
            else
            {
                $this->_id=$record->id;
                $record->online=1;//устанавливаем флаг онлайна пользователя
                $record->save();
                $this->setState('user_id', $record->id);
                $name = $record->name;
                if (!$name) $name = $record->login;
                $this->setState('first_name', $name);
                $this->setState('last_name', $record->family);
                $this->errorCode=self::ERROR_NONE;
            }
        }
        return !$this->errorCode;
    }





    /*добавляем метод для обращения к полю _id*/
    public function getId()
    {
        return $this->_id;
    }
}
