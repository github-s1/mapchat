<?php
class Notification
{
	private $ApiAccessKey;
	
	private $message;
	
	public function __construct($ApiAccessKey) {
		$this->ApiAccessKey = $ApiAccessKey;
	}
	
	public function setValue($name, $value)
	{
		$this->message[$name] = iconv('windows-1251','utf-8', $value);
		return $this;
	}
	
	public function SendPush($regestrationID=null,$msg=null)
    {     
		if (isset($regestrationID))
            $registrationIds = array($regestrationID);

        // prep the bundle
        /*
		$message = array
        (
            'message' 		=> $msg,
            'title'			=> 'This is a title. title',
            'subtitle'		=> 'This is a subtitle. subtitle',
            'tickerText'	=> 'Ticker text here...Ticker text here...Ticker text here',
            'vibrate'	=> 1,
            'sound'		=> 1
        );
		*/
		//$this->message['message'] = iconv('windows-1251','utf-8', $msg);
		$this->message['message'] = $msg;

        $fields = array
        (
            'registration_ids' 	=> $registrationIds,
            'data'				=> $this->message
        );

        $headers = array
        (
            'Authorization: key=' . $this->ApiAccessKey,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
	
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        curl_close( $ch );

        return $result;
    }
}