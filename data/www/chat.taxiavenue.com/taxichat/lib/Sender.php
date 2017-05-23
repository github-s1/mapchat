<?php

namespace Softline\Mobile\PHP\PushNotifications\iOS;

class Sender extends \Connector{

	protected $sProductionServer = "ssl://gateway.push.apple.com:2195";
	protected $sDevelopmentServer =  "ssl://gateway.sandbox.push.apple.com:2195";

	/**
	 * send message
	 * @param \Softline\Mobile\PHP\PushNotifications\iOS\Message $mMessage array Message or one Message
	 * @return array boolean
	 * @throws Exception
	 */
	public function send($oMessage){
		$aDevices = $oMessage->getDeviceId();
		$aRes = array();
		$connect = $this->getConnect();
		foreach($aDevices as $iKey=>$sDevice){
			$sMessage = $oMessage->getValidBinaryMessage($iKey);
			$aRes[$sDevice] = false;
			if ($sMessage && $connect) {
				$aRes[$sDevice] = fwrite($connect, $sMessage);
				$this->LogResult($sMessage.' - '.count($aDevices));
			}
		}
		$this->disconnect();
		
		return $aRes;
	
	}
	
		
	public function LogResult($text)
	{
		$str = $text.' - '.date('Y-m-d H:i:s', strtotime("now"));
		$fp = fopen('/srv/taxichat/taxichat/protected'.DIRECTORY_SEPARATOR.'result.txt', 'a');
		$test = fwrite($fp, $str.PHP_EOL);
		fclose($fp);
	}

}
?>