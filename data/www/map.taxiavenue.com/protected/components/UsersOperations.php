<?php
class UsersOperations
{	
	
	public static function GetMobileUserInfo( Users $user)
	{
		$Rez = array();
		if(!empty($user)) {  
			$Rez = $user->getAttributes();  
			
			if(!empty($user->idAvatar)) {
				$Rez['url_big'] ='http://' . Yii::app()->params->baseUrl . '/img/users_avatar/' . $user->idAvatar->big_photo;
                $Rez['url_small'] ='http://' . Yii::app()->params->baseUrl . '/img/users_avatar/small/' . $user->idAvatar->small_photo;
			}
			
			if(!empty($user->CityInfo)) {
				$Rez['city'] = $user->CityInfo->name_ru;
			}
		}	
		return $Rez;
	}
	
	public static function updateUser($id_user, $id_avatar=null, $name=null, $family=null, $sex=null, $age=null, $about=null, $telephone=null, $email=null, $city=null, $status=null){
		if (isset($id_user)) {
			$user = Users::model()->findByPk($id_user);
			if(!empty($user)) {
				if (isset($id_avatar)){
					$objAvatar = Avatar::model()->findByPk($id_avatar);
					if (empty($objAvatar)) {
						return array('error'=>array('error_code'=>13,'error_msg'=>self::ERROR_AVATAR_NOT_EXIST));
					} 	
				}
				
				$attr = array('id_avatar' => $id_avatar, 'name' => $name, 'family' => $family, 'sex' => $sex, 'age' => $age, 'about' => $about, 'telephone' => $telephone, 'email' => $email, 'city' => $city, 'status' => $status);
				$user->SetParams($attr);
				if ($user->save()){
					
					$UserInfo = self::GetMobileUserInfo($user);
					
					return $UserInfo;
				}
				else {
					return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_SAVE));
				}
			} else {
				return array('error'=>array('error_code'=>2,'error_msg'=>'Пользователя с указаным id не существует.'));
			}
		} else {
            return array('error' => array('error_code' => 2, 'error_msg' => $update::ERROR_FILDS_EMPTY));		
		}	
    }
	
	
	
}
