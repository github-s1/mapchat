<?php

/* 
 * 
 */

interface IOpenAuth
{   
    public function login();
    public function logOut();
    public function checkSign($getVars);
    public function getVars();
    public function getUid();
    public function getSystem();
    /**
     * @param string $name
     * @param string $family
     * @param string $sex
     * @param int $age
     * @param string $city
     */
    public function getUserInfo();
    
    public static function getAppId();
    public static function getSecretKey();
}