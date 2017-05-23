<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class InlangerMark implements IServiceMark
{
    /**
     * Url for request api
     */
    const URL = 'https://api.foursquare.com/v2/venues/search';
    
    const CLIENT_ID = 'ZQT1Q3CQXXI0TC1JFCBAM1X0O1WFQVMXQIYPIWAXL3VLPCGL';
    const SECRET_ID = 'CCD0E4LNX1FMFOBU3KJPYRPOBQMSE22OOUMBSIT3QFVVP5QS';
    
    const LIMIT = 5; //! Количество извлекаемых меток
    
    public $marks = array();

    public function fetchMarks(City $city) {
        $params = array(
            'client_id'   => self::CLIENT_ID,
            'client_secret'  => self::SECRET_ID,
            'v'  => '20130815',
            'll'    => $city->lat . ',' . $city->lng,
            'limit' => self::LIMIT
        );
        $result = $this->makeRequest($params);

        if (isset($result->response) and isset($result->response->venues)) $this->marks = $result->response->venues;
        //return $marks;
        //return $this->preparedMarks($marks);
    }

    public function getField($pos, $name) {
        $obj = $this->marks[$pos];
        $field = '';
        if ($name == 'name') $field = $obj->name;
        if ($name == 'lat') $field = $obj->location->lat;
        if ($name == 'lng') $field = $obj->location->lng;
        if ($name == 'address') $field = $obj->location->formattedAddress[0];
        if ($name == 'img_url') {
            if (isset($obj->categories[0]) and isset($obj->categories[0]->icon)) $field = $obj->categories[0]->icon->prefix . $obj->categories[0]->icon->suffix;
        }
        if ($name == 'mark_url') $field = (isset($obj->url) ? $obj->url : '');
        
        return str_replace('\'', '',$field);
    }
    
    /*public function preparedMarks(array $marks) {
        $result = array();
        foreach ($marks as $venue) {
            $result[] = array(
                'name' => $venue->name,
                'lat' => $venue->location->lat,
                'lng' => $venue->location->lng,
                'address' => $venue->location->formattedAddress[0],
                'img_url' => $venue->categories[0]->icon->prefix . $venue->categories[0]->icon->suffix,
                'mark_url' => (isset($venue->url) ? $venue->url : '')
            );
        }
        return $result;
    }*/

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
}