<?php

namespace Softline\Mobile\PHP\PushNotifications\iOS;

class Response  extends \Connector {

	protected $sProductionServer = "ssl://feedback.push.apple.com:2196";
	protected $sDevelopmentServer = "ssl://feedback.sandbox.push.apple.com:2196";

	public function getResponse() {
		$connect = $this->getConnect();
		stream_set_write_buffer($connect, 0);
		$aRes = array();
		while (!feof($connect)) {
			$data = fread($connect, 38);
			if($data) {
				$aFeedback = unpack("N1timestamp/n1length/H*devtoken", $data);
				$aRes[$aFeedback['devtoken']] = $aFeedback;
			}
		}
		return $aRes;
	}

}
?>