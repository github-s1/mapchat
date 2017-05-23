<?php

/**
 * 
 */
abstract class Check
{
    protected $_isAjax;
    protected $_isMobile;
    protected $_jsonFormat;
    protected $_strategy;
    protected $user;
    protected $_error = false;
    protected $_result;


    abstract protected function saveDataUser();
    abstract protected function notifyUser(); // Послать пользователю письмо на мыло, либо смс на мобильный
    abstract protected function getResult();

    protected function getValue($val) {
        return ($this->_jsonFormat) ? Yii::app()->request->getPost($val) : Yii::app()->request->getQuery($val);
    }
    
    protected function throwError($err) {
        $this->_error = $err;
        throw new CException($err);
    }
    
    /**
     * Вывод ошибки пользователю
     */
    public function showError() {
        if ($this->_jsonFormat) {
            $result = array('error'=>array('error_code'=>2,'error_msg'=> $this->_error));
            $res = array('response'=>$result);
            echo json_encode($res);
            exit;
        } else {
            throw new CHttpException('404', $this->_error);
        }
    }
    
    public function showResult() {
        if ($this->_jsonFormat) {
            $res = array('response'=>$this->_result);
            echo json_encode($res);
            exit;
        } else {
            Yii::app()->getRequest()->redirect(Yii::app()->getHomeUrl());
        }
    }

    public function __construct() {
        $this->_isAjax = Yii::app()->request->isAjaxRequest;
        $this->_isMobile = Yii::app()->request->getPost('mobile');
        $this->_jsonFormat = ($this->_isAjax || $this->_isMobile);
    }

    /**
     * Проверка пароля
     */
    public function check() {
        $id_user = $this->getValue('id_user');
        $animal = $this->getValue('animal');
        if(empty($id_user) or empty($animal)) $this->throwError(updateData::MISSING_DATA);
        $this->user = Users::model()->findByPk($id_user);
        if (!isset($this->user)) $this->throwError(updateData::ERROR_USER_NOT_EXIST);
        if ($this->user->confirm_code == '') $this->throwError(updateData::USER_ALREADY_ACTIVE);
        if ($this->user->confirm_code != $animal) $this->throwError(updateData::WRONG_ANIMAL);
        
        $time_live=$this->user->confirm_date-time();
        if($time_live > Yii::app()->params['time_code']) $this->throwError(updateData::ERROR_CONFIRM_CODE);
        
        $this->saveDataUser();
        
        $identity = new UserIdentity($this->user->login, $this->user->pass);

        if($identity->authenticate_register()){
            Yii::app()->user->login($identity);
        }
        
        $this->notifyUser();
        
        $this->_result = $this->getResult();
    }
    
    protected function generatePassword($number)
    {
        $arr = array('a','b','c','d','e','f',
            'g','h','i','j','k','l',
            'm','n','o','p','r','s',
            't','u','v','x','y','z',
            'A','B','C','D','E','F',
            'G','H','I','J','K','L',
            'M','N','O','P','R','S',
            'T','U','V','X','Y','Z',
            '1','2','3','4','5','6',
            '7','8','9','0','.',',',
            '(',')','[',']','!','?',
            '&','^','%','@','*','$',
            '<','>','/','|','+','-',
            '{','}','`','~');
        // Генерируем пароль
        $pass = "";
        for($i = 0; $i < $number; $i++)
        {
            // Вычисляем случайный индекс массива
            $index = rand(0, count($arr) - 1);
            $pass .= $arr[$index];
        }
        return $pass;
    }
}

