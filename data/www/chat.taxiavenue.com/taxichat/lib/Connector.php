<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Connector
 *
 * @author gener
 */
abstract class Connector {

	private static $instance;

	private $sCertificatePath;

	protected $sProductionServer;
	protected $sDevelopmentServer;

	protected $bIsProduction = true;

	protected $connect = null;

	/**
	 * @param string $sCertificatePath - path to *.pem certificate
	 * @return array message info
	 */
	public function __construct($sCertificatePath) {
		$this->sCertificatePath = $sCertificatePath;
	}

	public function getIsProduction() {
		return $this->bIsProduction;
	}

	public function setIsProduction($bValue) {
		$this->bIsProduction = $bValue;
		return $this;
	}

	protected function getServer() {
		if( $this->getIsProduction() ){
			return $this->sProductionServer;
		}
		return $this->sDevelopmentServer;
	}

	public function getConnect(){
		if( !$this->connect ){
			$this->connect();
		}
		return $this->connect;
	}

	protected function connect() {
		$streamContext = stream_context_create();

		stream_context_set_option($streamContext, 'ssl', 'local_cert', $this->sCertificatePath);
		$error = null;
		$errorString = null;
		$this->connect = stream_socket_client($this->getServer(), $error, $errorString, 60, STREAM_CLIENT_CONNECT, $streamContext);
		if (!$this->connect){
			throw new Exception("Failed to connect APNS: $error $errorString");
			die;
		}
		stream_set_blocking($this->connect, 0);
	}

	protected function disconnect() {
		fclose($this->connect);
		
	}
}
