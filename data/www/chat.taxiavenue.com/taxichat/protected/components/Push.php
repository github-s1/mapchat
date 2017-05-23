<?php

use Softline\Mobile\PHP\PushNotifications\iOS as iOS;

require_once(Yii::getPathOfAlias('webroot').'/lib/Message/Message.php');
require_once(Yii::getPathOfAlias('webroot').'/lib/Sender.php');
// класс для отправки пушей на Android и IOS 
class Push {

	private $iosCertificate = NULL;
	private $androidCode = NULL;
	
	private $TokinID = NULL;
	private $MobileOS = true;
	/*
	private $iosTokin = NULL;
	private $androidTokin = NULL;
	*/
	private $massage = '';
	private $params = array();
	
	private $notification = NULL;

	public function __construct($isDriverApp = true, $TokinID = null, $MobileOS = true) {
		if($isDriverApp) {
			$this->iosCertificate = Yii::getPathOfAlias('webroot') . "/cert/CertificateTaxiChatDeveloper.pem";
			//$this->iosCertificate = Yii::getPathOfAlias('webroot') . "/cert/CertificateTaxiChatProduction.pem";
			
			//$this->androidCode = 'AIzaSyALlHs53zlD6RtIRDRoBndvP_GKO7PO0Bw';
			//$this->androidCode = 'AIzaSyAuQr7bdpDmaERRrII3N7GYL5V2nGaVuI8';
			$this->androidCode = 'AIzaSyCCxgohNC-E2NK3QawQVrwx7u3ymv3RrQg';
			
		} else {
			$this->iosCertificate = Yii::getPathOfAlias('webroot') . "/cert/CertificateTaxiChatClientDevelopment.pem";
			//$this->iosCertificate = Yii::getPathOfAlias('webroot') . "/cert/CertificateTaxiChatClientProduction.pem";
		
			$this->androidCode = 'AIzaSyA1CRkR_bJm291OiZSY8e_XIpoxgwj5BEo';
		}
		$this->TokinID = $TokinID;
		$this->MobileOS = $MobileOS;
	}
	
	public function setValue($name, $value)
	{	
		if(!empty($name)) {
			$this->params[$name] = $value;
		}	
		return $this;
	}
	
	public function setMassage($massage)
	{	
		$this->massage = $massage;
		return $this;
	}
	
	public function sendPush() {
		$res = null;
		if(!empty($this->TokinID) && strlen($this->TokinID) > 8 && $this->MobileOS) {
			$oMessage = iOS\Message::getInstance()->setAlertBody($this->massage)->setBadge(1)->setSound('default')->setDeviceId($this->TokinID);
			if(!empty($this->params)) {
				foreach($this->params as $param => $value) {
					$oMessage->setValue($param, $value);
				}
			}
			$this->notification = new iOS\Sender($this->iosCertificate);
			$this->notification->setIsProduction(false);
			$res = $this->notification->send($oMessage);
			
			
		} elseif(!empty($this->TokinID) && strlen($this->TokinID) > 8 && !$this->MobileOS) {
			$this->notification = new Notification($this->androidCode);
			if(!empty($this->params)) {
				foreach($this->params as $param => $value) {
					$this->notification->setValue($param, $value);
				}
			}
			$res = $this->notification->SendPush($this->TokinID, $this->massage);
		}
		return $res; 
	}

}
