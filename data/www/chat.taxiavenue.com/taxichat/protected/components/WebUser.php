<?php

class WebUser extends CWebUser {
    private $_model = null;
 
    function getRole() {
        if($user = $this->getModel()){
            // в таблице User есть поле role
            return $user->id_type;
        }
    }
 
    private function getModel(){
        if (!$this->isGuest && $this->_model === null){
			//$this->authTimeout = 2629743;
            $this->_model = Users::model()->findByPk($this->id, array('select' => 'id_type'));
        }
        return $this->_model;
    }
}