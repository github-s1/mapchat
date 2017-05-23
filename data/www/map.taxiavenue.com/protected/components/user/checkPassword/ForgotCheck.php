<?php

/**
 * 
 */
class ForgotCheck extends Check
{
    private $password;
    protected function saveDataUser() {
        $this->user->confirm_code='';
        $this->user->confirm_date='0000-00-00 00:00:00';
        $this->user->active='y';
        $this->password = $this->generatePassword(8);
        $this->user->pass = crypt($this->password);
        
        // Для отображения попапа после редиректа
        if (!$this->_jsonFormat) {
            Yii::app()->session->add('popup_forgotChecked', array(
                'login' => $this->user->login,
                'id'    => $this->user->id
            ));
        }
        
        $this->user->save();
    }
    
    protected function notifyUser() {
        if (!$this->_isMobile) {
            $to= $this->user->login;
            /* тема/subject */
            $subject = "Новый пароль. Онлайн карта";
            /* сообщение */
            $message = '<html><body>Добрый день!
            Вам сгенерированый новый пароль - '.$this->password.'.
            Вы всегда сможете поменять этот пароль в своем личном кабинете.</body></html>';
            Mail::send($to, $subject, $message);
        } else {
            $api = new MainSMS();
            $message = 'Вам сгенерированый новый пароль - ' . $this->password;
            if ($api->sendSMS($this->user->login, $message , 'onlinemap')) {
                $this->logMsg('SEND_SMS_SUCCESS', $api->getResponse());
            } else {
                $this->logMsg('SEND_SMS_FAIL', $api->getResponse());
                $this->throwError('Смс не отправлено. (код: 2)');
            }
        }
    }
    
    protected function getResult() {
        if ($this->_isMobile) {
            $result = 'success';
        } else {
            $result = Controller::GetUserById($this->user->id);
            $result['pass'] = $this->password;
        }
        
        return $result;
    }
    
    public function logMsg($fileName, $str = null){
        if ($str === null) {
            $str = $fileName;
            $fileName = 'log';
        }
        if ($str === true)  $str = 'true';
        if ($str === false) $str = 'false';
        if ($str === '')    $str = '""';
        if ($str === ' ')   $str = '" "';
        if (is_resource($str)) $str = '{resource}';
        if (is_array($str) or is_object($str)) {
            $str = print_r($str, true);
        }
        file_put_contents(Yii::app()->basePath . '/runtime/' . $fileName . '.txt', date('j.m H:i:s: ') . $str . "\n", FILE_APPEND);
    }
}

