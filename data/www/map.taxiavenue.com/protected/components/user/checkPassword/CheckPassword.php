<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CheckPassword
{
    /**
     * @param type = register | forgot
     */
    private $_strategy;

    public function __construct(Check $strategy) {
        $this->_strategy = $strategy;
    }
    
    // проверка животного при восстановлении пароля
    public function doCheck() {
        $this->_strategy->check();
    }
    
    public function showError() {
        $this->_strategy->showError();
    }
    
    public function showResult() {
        $this->_strategy->showResult();
    }
}