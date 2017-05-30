<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Mailru implements IOpenAuth
{
    public $appId = '728852';
    const APP_ID = '728852';
    
    protected $secretKey = '0964f0987b3631f1477feea66ff64dc4';
    const SECRET_KEY = '0964f0987b3631f1477feea66ff64dc4';
    
    protected $pivateKey = '7a3d0d9ab06cd93c5bbe40fad2bc6ce8';

    private $_system = 'mailru';
    private $_member = array();

    public function login() {
        
    }
    
    public function logOut() {
        
    }
    
    public function checkSign($vars) {
        if (!empty($vars['error']) and $vars['error'] == 'access_denied') {
            //throw new Exception('Вы отказались от авторизации через Mail.ru.');
            return false;
        }
        if (!empty($vars['error']) or empty($vars['code'])) {
            throw new Exception('Ошибка при авторизации.');
        }

        // Создать контекст и инициализировать POST запрос
        $query = 'client_id=' . $this->appId . '&client_secret=' . $this->secretKey . '&code=' . $vars['code'] . '&grant_type=authorization_code&redirect_uri=http://185.159.129.150:8085/auth/openAuth/system/mailru';
        $context = stream_context_create(array(
                                              'http' => array(
                                                  'method' => 'POST',
                                                  'header' => 'Content-Type: application/x-www-form-urlencoded' . PHP_EOL,
                                                  'content' => $query,
                                              )));

        $user = (array)json_decode(file_get_contents('https://connect.mail.ru/oauth/token', false, $context));
        if (!$user or !empty($user['error'])) {
            return false;
        }

        if ($user['expires_in'] < 1) {
            return false;
        }
        $this->_member = array(
            'x_mailru_vid'  => $user['x_mailru_vid'],
            'access_token'  => $user['access_token'],
            'refresh_token' => $user['refresh_token'],
            'expires_in'    => (int) $user['expires_in'], // expires_in => int(86367)
        );
        return true;
    }

    public function getUserInfo() {
        if (empty($this->_member)) {
            throw new Exception('Ошибка при авторизации.');
        }
        $openUserId = $this->_member['x_mailru_vid'];
        if ($openUserId < 1) {
            throw new Exception('Недопустимое значение ID пользователя.');
        }
        $params = array(
            'app_id' => $this->appId,
            'format' => 'json',
            'method' => 'users.getInfo',
            'secure' => '1',
            'uid' => $openUserId,
        );

        $sig = md5($this->implodeValues($params, '') . $this->secretKey);
        $params['sig'] = $sig;
        $response = (array)json_decode(file_get_contents('http://www.appsmail.ru/platform/api?' . $this->implodeValues($params, '&')));
        if (empty($response) || empty($response[0])) {
            throw new Exception('Ошибка при авторизации.');
        }
        $user = $response[0];
        return array(
            'name' => $user->first_name,
            'family' => $user->last_name,
            'sex' => $user->sex == 0 ? 'm' : 'w',
            'age' => isset($user->age) ? $user->age : '',
            'city' => isset($user->city->title) ? $user->city->title : '',
            'email' => isset($user->email) ? $user->email : '',
        );
    }
    
    public function getVars() {
        return $_GET;
    }
    
    public function getUid() {
        if (empty($this->_member)) return 0;
        return $this->_member['x_mailru_vid'];
    }
    
    public function getSystem() {
        return $this->_system;
    }
    
     /**
     * Объединение пар ключ=значение массива в строку
     * @param reference array $params
     * @param string $delimeter -- Разделитель пар
     * @return string
     */
    function implodeValues(&$params, $delimeter = '&')
    {
        $pice = array();
        ksort($params);
        foreach ($params as $k => $v) {
            $pice[] = $k . '=' . $v;
        }
        return implode($delimeter, $pice);
    }
    
    public static function getAppId() {
        return self::APP_ID;
    }
    
    public static function getSecretKey() {
        return self::SECRET_KEY;
    }
}