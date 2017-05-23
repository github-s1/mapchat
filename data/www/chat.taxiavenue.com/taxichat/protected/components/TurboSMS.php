<?php
// класс осуществляет отпраку СМС через TurboSMS
class TurboSMS {
	private $client = NULL;
	private $massage = NULL;
	private $phone = NULL;
	private $sender = 'TaxiChat';
	private $login = 'taxichat';
	private $password = 'taxichat';

	public function __construct($login, $password, $sender) {
		$this->login = $login;
		$this->password = $password;
		$this->sender = $sender;
	}
	
	public function setMassage($value)
	{	
		$this->massage = $value;
		return $this;
	}
	
	public function setPhone($value)
	{	
		$this->phone = $value;
		return $this;
	}
	
	public function sendMassage() {
		header ('Content-type: text/html; charset=utf-8');
		$this->client = new SoapClient ('http://turbosms.in.ua/api/wsdl.html');
		
		$auth = Array (
			'login' => $this->login,
			'password' => $this->password
		);
		$result = $this->client->Auth($auth);

		$sms = Array (
			'sender' => $this->sender,
			'destination' => '+38'.$this->phone,
			'text' => $this->massage
		); 
		$result = $this->client->SendSMS ($sms); 
		return $result; 
	}

}
