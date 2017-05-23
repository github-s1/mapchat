<?php

namespace Softline\Mobile\PHP\PushNotifications\iOS;
/**
 * Build message from iOS Push notification
 *
 * @author Evgeniy Kalyada <evgeniy.kalyada@softline.ru>
 */
class Message {

	private static $instance;
	private $iMaxMessageSize = 256;

	/**
	 * all fields for Push Notifications
	 * @var type 
	 */
	private $aMessage = array(
		'aps' => array(
			'alert' => array (
				'body' => '',
			),
			'sound' => null,
			'badge' => 0
		)
	);

	private $aDeviceId;

	public static function getInstance() {

		if (!self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Set text notification
	 * @param string $sAlert
	 * @return \Softline\Mobile\PHP\PushNotifications\iOS\Message
	 */
	public function setAlertBody($sBody) {
		$this->setValueByKeyPath('aps.alert.body', $sBody);
		return $this;
	}

	public function getAlertBody() {
		return $this->getValueByKeyPath('aps.alert.body');
	}

	/**
	 * Set device id
	 * @param string $mDeviceId
	 * @return \Softline\Mobile\PHP\PushNotifications\iOS\Message
	 */
	public function setDeviceId($mDeviceId) {
		if( !is_array($mDeviceId) ){
			$mDeviceId = array($mDeviceId);
		}
		$this->aDeviceId = $mDeviceId;
		return $this;
	}

	public function getDeviceId($iKey = null) {
		if( $iKey >= 0 && $iKey !== null ){
			return $this->aDeviceId[$iKey];
		}
		return $this->aDeviceId;
	}

	/**
	 * path to sound which will be played after recive pusn notificaion in device
	 * @param strin $sSound
	 * @return \Softline\Mobile\PHP\PushNotifications\iOS\Message
	 */
	public function setSound($sSound) {
		$this->setValueByKeyPath('aps.sound', $sSound);
		return $this;
	}

	public function getSound() {
		return $this->getValueByKeyPath('aps.sound');
	}

	/**
	 * The number to display as the badge of the application icon.
	 * If this property is absent, the badge is not changed. To remove the badge, set the value of this property to 0.
	 * @param int $iBadge
	 * @return \Softline\Mobile\PHP\PushNotifications\iOS\Message
	 */
	public function setBadge($iBadge) {
		$this->setValueByKeyPath('aps.badge', (int)$iBadge);
		return $this;
	}

	public function getBadge() {
		return $this->getValueByKeyPath('aps.badge');
	}
	
	public function setValue($paramName, $paramValue) {
		$this->setValueByKeyPath($paramName, $paramValue);
		return $this;
	}
	
	/**
	 * additional parameter which will be send to iOS application. This is not required parameter
	 * @param string $sKeyPath path to value^ example (aps.alert.body) use dot as delimeter keys
	 * @param string $sValue
	 * @return \Softline\Mobile\PHP\PushNotifications\iOS\Message
	 */
	public function setAdditional($sKeyPath, $sValue) {
		$this->setValueByKeyPath($sKeyPath, $sValue);
		return $this;
	}

	/**
	 * additional parameter which will be send to iOS application. This is not required parameter
	 * @param string $sKeyPath path to value^ example (aps.alert.body) use dot as delimeter keys
	 * @return type 
	 */
	public function getAdditional($sKeyPath) {
		return $this->getValueByKeyPath($sKeyPath);
	}

	protected function setValueByKeyPath($sKeyPath, $mValue) {
		$aKeyPathArray = $this->parsePath($sKeyPath);
		$this->aMessage = $this->setValueByKeyPathArray($aKeyPathArray, $mValue, $this->aMessage);
	}

	protected function getValueByKeyPath($sKey) {
		$aKeyPathArray = $this->parsePath($sKey);
		return $this->getValueByKeyPathArray($aKeyPathArray, $this->aMessage);
	}

	private function setValueByKeyPathArray($mKeyPathArray, $mValue, $aTree){
		$sCurrentKey = $mKeyPathArray;
		if( is_array($mKeyPathArray) ){
			$sCurrentKey = current($mKeyPathArray);
		}
		if(count($mKeyPathArray) > 1){
			if( !isset($aTree[$sCurrentKey]) ){
				$aTree[$sCurrentKey] = null;
			}
			array_shift($mKeyPathArray);
			$aTree[$sCurrentKey] = $this->setValueByKeyPathArray($mKeyPathArray, $mValue, $aTree[$sCurrentKey]);
		}
		else {
			$aTree[$sCurrentKey] = $mValue;
		}
		return $aTree;
	}

	private function getValueByKeyPathArray($mKeyPathArray, $aTree){
		$sCurrentKey = $mKeyPathArray;
		if( is_array($mKeyPathArray) ){
			$sCurrentKey = current($mKeyPathArray);
		}
		if(count($mKeyPathArray) > 1){
			if( isset($aTree[$sCurrentKey]) ){
				array_shift($mKeyPathArray);
				return $this->getValueByKeyPathArray($mKeyPathArray, $aTree[$sCurrentKey]);
			}
			else {
				return null;
			}
			
		}
		return $aTree[$sCurrentKey];
	}

	private function parsePath($sPath){
		return explode('.', $sPath);
	}

	/**
	 * array fileds message
	 * @return array
	 */
	public function toArray(){
		return $this->aMessage;
	}

	private function prepareUTF8($matches){
		return json_decode('"\u'.$matches[1].'"');
	}

	public function toString(){
		return preg_replace_callback('/\\\u([01-9a-fA-F]{4})/', 'self::prepareUTF8',
			json_encode( $this->toArray() )
		);
	}

	protected function getBinaryMessage($iKeyDeviceId){
		$sMessage = $this->toString();
		$sMessage = chr(0) . pack('n', 32) . pack('H*', $this->getDeviceId($iKeyDeviceId)) . pack('n', mb_strlen($sMessage, '8bit')) . $sMessage;
		return $sMessage;
	}

	/**
	 * https://developer.apple.com/library/ios/documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/Chapters/ApplePushService.html
	 * @param type $sDeviceId
	 * @return type 
	 */
	public function getValidBinaryMessage($iKeyDeviceId = null) {
		$sMessage = $this->getBinaryMessage($iKeyDeviceId);
		return  $this->getBinaryMessageSize($iKeyDeviceId)<= $this->iMaxMessageSize ? $sMessage : null;
	}

	public function getBinaryMessageSize($iKeyDeviceId){
		$sMessage = $this->getBinaryMessage($iKeyDeviceId);
		return mb_strlen( $sMessage, '8bit' );
	}
}


?>