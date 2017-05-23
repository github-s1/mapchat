<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class LoginForm extends CFormModel
{
    public $login;
    public $pass;
    public $rememberMe;

    private $_identity;

    /**
     * Declares the validation rules.
     * The rules state that username and password are required,
     * and password needs to be authenticated.
     */
    public function rules()
    {
        return array(
            // username and password are required
            array('login, pass', 'required'),
            // rememberMe needs to be a boolean
            array('rememberMe', 'boolean'),
            // password needs to be authenticated
            array('pass', 'authenticate'),
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return array(
            'rememberMe'=>'Remember me next time',
        );
    }

    /**
     * Authenticates the password.
     * This is the 'authenticate' validator as declared in rules().
     */
    public function authenticate($attribute,$params) {
        if(!$this->hasErrors())
        {
            $this->_identity = new UserIdentity($this->login,$this->pass);
            if(!$this->_identity->authenticate())
                $this->addError('pass','Incorrect username or password.');
        }
    }

    public function login($type = null) {

        $this->_identity = new UserIdentity($this->login,$this->pass);

        if($this->_identity->authenticate($type)) {
            if($type <= 2) {
                $duration = 3600 * 24 * 50;
                Yii::app()->user->login($this->_identity, $duration);
            } else
                Yii::app()->user->login($this->_identity);
            return true;
        }
        else {
            echo $this->_identity->errorMessage;
            return false;
        }
    }


}