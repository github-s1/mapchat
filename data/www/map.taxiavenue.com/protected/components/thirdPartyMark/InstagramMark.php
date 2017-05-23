<?php

/** 
 * 
 */

class InstagramMark implements IServiceMark
{
    const URL = 'https://api.instagram.com/v1/media/search';
    
    const CLIENT_ID = '48890765b78f4cb78cfc0b6449e9393a';
    const SECRET_ID = '82c659a988df4da18646266aec8fc9bb';
    
    public $marks = array();
    
    public function fetchMarks(City $city) {
        $params = array(
            'client_id' => self::CLIENT_ID,
            'lat' => $city->lat,
            'lng' => $city->lng
        );
        $result = $this->makeRequest($params);
        if (isset($result->data)) $this->marks = $result->data;
    }

    public function getField($pos, $name) {
        $obj = $this->marks[$pos];
        $field = '';
        if ($name == 'name') $field = $this->getUserName ($obj);
        if ($name == 'lat') $field = $obj->location->latitude;
        if ($name == 'lng') $field = $obj->location->longitude;
        if ($name == 'address') $field = 'xxx';
        if ($name == 'img_url') $field = 'http://skimx.se/wp-content/uploads/2015/04/352677-instagram-logo.jpg';//$this->getImageUrl($obj);
        if ($name == 'mark_url') $field = $this->getMarkUrl($obj);
        return str_replace('\'', '',$field);
    }

    /**
     * Make request to Panoramio api
     */
    private function makeRequest($params) {
        $getStr = '';
        foreach ($params as $key => $value) {
            $getStr .= ($key . '=' . $value . '&');
        }
        $getStr = substr($getStr, 0, -1);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, self::URL . '?' . $getStr);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        $out = curl_exec($curl);
        curl_close($curl);
        return json_decode($out);
    }
    
    private function getImageUrl($obj) {
        if (!isset($obj->images)) return '';
        if (isset($obj->images->standard_resolution)) return $obj->images->standard_resolution->url;
        if (isset($obj->images->low_resolution)) return $obj->images->low_resolution->url;
        if (isset($obj->images->thumbnail)) return $obj->images->thumbnail->url;
        return '';
    }
    
    private function getUserName($obj) {
        $name = '';
        if (isset($obj->user)) {
            if (isset($obj->user->username)) $name = $obj->user->username;
        }
        if (isset($obj->caption)) {
            if (isset($obj->caption->from->username)) $name = $obj->caption->from->username;
        }
        return $name;
    }
    
    private function getMarkUrl($obj) {
        $url = '';
        //if (isset($obj->user) and isset($obj->user->username)) $url = 'https://instagram.com/' . $obj->user->username;
        $url = $obj->link;
        return $url;
    }
}