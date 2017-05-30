<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Facebook implements IOpenAuth
{
    public $appId = '1537195736551607';
    const APP_ID = '1537195736551607';
    
    protected $secretKey = '0fb031e88fff535c8eecac9e7bace82f';
    const SECRET_KEY = '0fb031e88fff535c8eecac9e7bace82f';

    //protected $vkApiUrl = 'https://oauth.vk.com';
    
    private $_system = 'facebook';

    private $_member = array();
    
    public function login() {}
    public function logOut() {}
    
    public function checkSign($vars) {
        $user = file_get_contents('https://graph.facebook.com/oauth/access_token?client_id=' . $this->appId . '&redirect_uri=' . urlencode('http://185.159.129.150:8085/auth/openAuth/system/facebook') . '&client_secret=' . $this->secretKey . '&code=' . $vars['code']);
        if (!$user) {
            //$url = 'https://graph.facebook.com/oauth/access_token?client_id=' . $this->appId . '&redirect_uri=' . urlencode(_Site::$host . '/index/open_auth/type/facebook/p/1') . '&client_secret=' . $this->secretKey . '&code=' . $vars['code'];
            return false;
        }

        $params = null;
        parse_str($user, $params);
        if ($params['expires'] < 1) {
            return false;
        }
        $this->_member = array(
            'access_token' => $params['access_token'],
            'expires' => (int)$params['expires'], // expires => int(86367)
        );
        return true;
    }
    
    public function getUserInfo() {
        if (empty($this->_member)) {
            throw new Exception('Ошибка при авторизации.');
        }
        $user = (array)json_decode(file_get_contents('https://graph.facebook.com/me?access_token=' . $this->_member['access_token']));
        if (!$user) {
            throw new Exception('Ошибка при авторизации.');
        }
        $openUserId = $user['id'];
        if ((int)$openUserId < 1) {
            throw new Exception('Недопустимое значение ID пользователя.');
        }
        $this->_member['id'] = $user['id'];
        return array(
            'name' => $user['first_name'],
            'family' => $user['last_name'],
            'sex' => ($user['gender'] == 'male' ? 'm' : 'w'),
            'age' => '',
            'city' => '',
            'email' => isset($user['email']) ? $user['email'] : '',
        );
    }
    
    public function getVars() {
        return $_GET;
    }
    
    public function getUid() {
        if (empty($this->_member)) return 0;
        return $this->_member['id'];
    }
    
    public function getSystem() {
        return $this->_system;
    }
    
    public static function getAppId() {
        return self::APP_ID;
    }
    
    public static function getSecretKey() {
        return self::SECRET_KEY;
    }
}