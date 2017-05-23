<?php

/**
 * 
 */
class RegisterCheck extends Check
{
    protected function saveDataUser() {
        $this->user->confirm_code='';
        $this->user->confirm_date='0000-00-00 00:00:00';
        $this->user->active='y';
        $this->user->save();
    }
    
    /**
     * При регистрации пока не шлем уведомление на мыло
     */
    protected function notifyUser() {
        
    }
    
    protected function getResult() {
        $result = Controller::GetUserById($this->user->id);
        return $result;
    }
}
