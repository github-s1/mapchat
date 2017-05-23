<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class OpenAuth
{
    private $_system;
    
    public function __construct(IOpenAuth $system) {
        $this->_system = $system;
    }
    
    /**
     * Авторизация пользователя через соцсети
     */
    public function auth() {
        $vars = $this->_system->getVars();
        if (!$this->_system->checkSign($vars)) {
            $this->_system->logout();
            return false;
        }
        
        //$userId = $this->_system->login($vars);
        $userInfo = $this->_system->getUserInfo();
        $user = $this->_getUserFromDb();
        if (!$user) {
            // Регистрация нового пользователя
            $user = new Users();
        }
        $user = $this->_saveUserInfo($user, $userInfo);
        if ($user->getErrors()) throw new CHttpException(404, 'Произошла ошибка при сохранении пользователя.');
        return $user;
    }

    /**
     * Обновить/сохранить инфу пользователя
     */
    protected function _saveUserInfo(Users $user, $userInfo) {
        foreach ($userInfo as $key => $value) {
            $user->$key = $value;
        }
        $user->setScenario('openAuth');
        if ($user->isNewRecord) {
            $user->login = !empty($user->email) ? $user->email : rand(10, 99999);
            $user->pass = 'social';
            $user->soc_register = $this->_system->getSystem();
            $user->soc_id = $this->_system->getUid();
            $user->date_register = date('Y-m-d H:i:s');
            $user->active='y';
        }
        $user->save();
        return $user;
    }


    protected function _getUserFromDb() {
        //ALTER TABLE `users` ADD `soc_id` VARCHAR(22) NULL DEFAULT NULL COMMENT 'id в соцсети' AFTER `soc_register`;
        $uid = $this->_system->getUid();
        $system = $this->_system->getSystem();
        return Users::model()->findByAttributes(array('soc_id' => $uid, 'soc_register' => $system));
    }


    /**
     * Возвращает определенный обьект класса
     * @param string vkontakte|odnoklassniki|facebook
     * @return obj Vkontakte|Odnoklassniki|Facebook
     */
    public static function getClassSystem($system) {
        $className = ucfirst($system);
        
        Yii::$enableIncludePath = false;
        if (class_exists($className)) {
            return new $className;
        }
        throw new CHttpException(404, 'Класса ' . $className . ' не существует.');
    }
}