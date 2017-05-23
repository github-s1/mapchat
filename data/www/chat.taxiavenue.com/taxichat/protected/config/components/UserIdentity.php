<?php

class UserIdentity extends CUserIdentity {
    // Будем хранить id.
    protected $_id;
 
    // Данный метод вызывается один раз при аутентификации пользователя.
    public function authenticate($type = 3){
		
        // Производим стандартную аутентификацию, описанную в руководстве.

		if($type <= 2) {
			$username = 'phone';
			$condition_str = ' AND id_type =?';
		} else {
			$username = 'nickname';	
			$condition_str = ' AND id_type >?';
			$type = 2;		
		}
		$user = Users::model()->find('LOWER('.$username.')=?'.$condition_str, array(strtolower($this->username), $type));
		
       if(($user===null) || ($user->password!==crypt($this->password,$user->password)))
       {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
       } else
       {	
			/*
			if($user->is_online) {
				$this->errorCode = 'Пользователь сейчас в сети.';
				return !$this->errorCode;
			}
			*/
			$user_status = UserStatus::GetUserById($user->id);
				
			if(!empty($user_status)) {
				//$user = Users::model()->findByPk($user_status->id_user);
					
				if($user->id_type == 1 || $user->id_type == 2) {
					
				//	if($user_status->id_status == 3 && $user_status->moderation != 0) {
						$this->_id = $user->id;
						$this->username = $user->nickname;
						$this->errorCode = self::ERROR_NONE;					
				/*	} else {
						$this->errorCode = self::ERROR_USERNAME_INVALID;
					}	
					*/
				}  else {
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