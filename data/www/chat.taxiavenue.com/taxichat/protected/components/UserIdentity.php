<?php

class UserIdentity extends CUserIdentity {
    // Будем хранить id.
    protected $_id;
 
    // Данный метод вызывается один раз при аутентификации пользователя.
    public function authenticate($type = 3){
		// водителей и клиентов аутентифицируем по телефону, остальных по никнейму
		if($type <= 2) {
			$username = 'phone';
			$user = Users::model()->find('LOWER('.$username.')=? AND id_type=?', array(strtolower($this->username), $type));
		} else {
			$username = 'nickname';	
			$user = Users::model()->find('LOWER('.$username.')=? AND id_type>?', array(strtolower($this->username), 2));
		}
		// проверяем правильный ли пароль
       if(($user===null) || ($user->password!==crypt($this->password,$user->password)))
       {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
       } else
       {	
			$user_status = UserStatus::model()->findByAttributes(array('id_user' => $user->id));
				
			if(!empty($user_status)) {
				
				$user = Users::model()->findByPk($user_status->id_user);
					
				if(!empty($user) && ($user->id_type == 1 || $user->id_type == 2)) {

					//проверяем статус водителя а также не забанен ли он
					//	if($user_status->id_status == 3 && $user_status->moderation != 0) {
							$this->_id = $user->id;
							$this->username = $user->nickname;
							$this->errorCode = self::ERROR_NONE;					
					/*	} else
							$this->errorCode = self::ERROR_USERNAME_INVALID;
					*/
				} else {
					$this->errorCode = self::ERROR_USERNAME_INVALID;
				}	
			} else {	
				
				$this->_id = $user->id;
				$this->username = $user->nickname;
				$this->errorCode = self::ERROR_NONE;
			}

       }
       return !$this->errorCode;
    }
	
    public function getId(){
        return $this->_id;
    }
}