<?php

class WebcamsMark implements IServiceMark
{
	 /**
     * Url for request api
     */
    var $devid = '0daac465c77c3f04e5435f6b492f3f3e';
    var $url = 'http://api.webcams.travel/rest?';
	
	public $marks = array();
	
	 public function fetchMarks(City $city) {
            $list = $this->listNearby($city->lat, $city->lng, $radius = 50, $unit = 'deg', $per_page = 1000, $page = 1);
            $this->marks = $list['webcams']['webcam'];
        }
	
	public function getField($pos, $name) {
        $obj = $this->marks[$pos];
        $field = '';
        if ($name == 'name') $field = $obj['title']; //$this->getUserName ($obj);
        if ($name == 'lat') $field = $obj['latitude'];
        if ($name == 'lng') $field = $obj['longitude'];
        if ($name == 'address') $field = 'xxx';
        if ($name == 'img_url') $field = 'http://www.webcams.travel/icons/apple-touch-icon.png';//$obj['thumbnail_url'];
        if ($name == 'mark_url') $field = $obj['url'];
        return str_replace('\'', '',$field);
    }
    
     private function buildQuery( $data )
    {
        $data  = (array) $data;
        $query = array(
            'format' => 'php',
            'devid'  => $this->devid,
            'method' => $data['method']
        );
        $query = array_merge( $query, $data );
        return http_build_query( $query );
    }
    
    private function execQuery( $query )
    {
        $ch = curl_init();
        curl_setopt_array( $ch, array(
            CURLOPT_URL            => $query,
            CURLOPT_VERBOSE        => true,
            CURLOPT_RETURNTRANSFER => true
        ) );
        $response = unserialize( curl_exec( $ch ) );
        curl_close( $ch );
        return $response;
    }
    
     function listNearby( $lat, $lng, $radius = 0.5, $unit = 'deg', $per_page = 5, $page = 1 )
    {
        $query = $this->url . $this->buildQuery( array(
                'method'   => 'wct.webcams.list_nearby',
                'lat'      => $lat,
                'lng'      => $lng,
                'radius'   => $radius,
                'unit'     => $unit,
                'per_page' => $per_page,
                'page'     => $page
            ) );
        return $this->execQuery( $query );
    }
	
}