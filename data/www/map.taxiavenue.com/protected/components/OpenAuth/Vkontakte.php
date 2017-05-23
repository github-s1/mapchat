<?php

/* 
 * Серверная авторизация
 */

class Vkontakte implements IOpenAuth
{
    public $appId = '4696521';
    const APP_ID = '4696521';
    
    protected $secretKey = 'RaTExvFU7hP7fP4mGgFM';
    const SECRET_KEY = 'RaTExvFU7hP7fP4mGgFM';
    
    //const VK_API_URL = 'https://api.vk.com/method';
    protected $vkApiUrl = 'https://oauth.vk.com';
    
    private $_system = 'vkontakte';

    private $_member = array();

    /**
     * 
     */
    public function login() {
        
    }
    
    public function logOut() {
        
    }
    
    public function checkSign($vars) {
        if (!empty($vars['error_reason']) and $vars['error_reason'] == 'user_denied') {
            //throw new CHttpException(404, 'Вы отказались от авторизации через Вконтакте.');
            return false;
        }
        if (!empty($vars['error']) or empty($vars['code'])) {
            //throw new CHttpException('Ошибка при авторизации.');
            return false;
        }
        $user = (array) @json_decode(file_get_contents($this->vkApiUrl . '/access_token?client_id=' . $this->appId . '&client_secret=' . $this->secretKey . '&code=' . $vars['code'] . '&redirect_uri=http://map.taxiavenue.com/auth/openAuth/system/vkontakte'));
        
        if (isset($user['access_token'])) {
            $this->_member = array(
                'user_id' => (int) $user['user_id'],
                'access_token' => $user['access_token'],
                'expires_in' => (int) $user['expires_in'], // expires_in => int(86367)
                'email' => isset($user['email']) ? $user['email'] : '',
            );
            return true;
        }
        return false;
    }
    
    /**
     * Сделать вызов API вконтакте
     */
    protected function makeCall($params, $call) {
        $params['application_key'] = $this->applicationKey;
        $params['format'] = 'JSON';
        // TODO
    }

    public function getVars() {
        return $_GET;
    }
    
    public function getUid() {
        if (empty($this->_member)) return 0;
        return $this->_member['user_id'];
    }
    
    public function getSystem() {
        return $this->_system;
    }
    
    public function getUserInfo() {
        $user = (array) json_decode(file_get_contents('https://api.vk.com/method/users.get?uids=' . $this->_member['user_id'] . '&fields=email,first_name,last_name,nickname,screen_name,sex,bdate,city,country,timezone,photo,photo_medium,photo_big,has_mobile,rate,contacts,education,online,counters&access_token=' . $this->_member['access_token'] . '&v=5.27'));
        if (!empty($user['error']) or empty($user['response'][0])) {
            throw new CHttpException(404, 'Информация о пользователе недоступна.');
        }
        $user = $user['response'][0];
        $gender = 'm';
        if ($user->sex == 1) $gender = 'w';
        return array(
            'name' => $user->first_name,
            'family' => $user->last_name,
            'sex' => $gender,
            //'age' => isset($user->bdate) ? $user->bdate : '',
            'age' => '',
            'city' => isset($user->city->title) ? $user->city->title : '',
            'email' => isset($user->email) ? $user->email : '',
        );
    }
    
    public static function getAppId() {
        return self::APP_ID;
    }
    
    public static function getSecretKey() {
        return self::SECRET_KEY;
    }
}