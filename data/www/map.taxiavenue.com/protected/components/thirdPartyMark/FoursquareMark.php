<?php

/* 
 * Получить метки (по границам "bounds" карты) из panoramio
 * @see http://korinets.name/panoramio-api.html
 * @see http://www.panoramio.com/api/data/api.html
 */

class FoursquareMark implements IServiceMark
{
	 /**
     * Url for request api
     */
    const URL = 'https://api.foursquare.com/v2/venues/search';
	
	const token = 'HSWGCTZKSBEW3Q0HRUEIGP1VRR2YANIV43IRUB31QMS0015E';
	
	public $marks = array();
	
	 public function fetchMarks(City $city) {
        $params = array(
            'oauth_token' => self::token,
            'll' => $city->lat . "," . $city->lng,
			'v' => "20160614",
			'radius' => "50000",
			'intent' => "browse"
        );
        $result = $this->makeRequest($params);
        if (isset($result->meta->code)) $this->marks = $result->response->venues;
    }
	
	public function getField($pos, $name) {
        $obj = $this->marks[$pos];
        $field = '';
        if ($name == 'name') $field = $obj->name; //$this->getUserName ($obj);
        if ($name == 'lat') $field = $obj->location->lat;
        if ($name == 'lng') $field = $obj->location->lng;
        if ($name == 'address') $field = ((isset($obj->location->address)) ? $obj->location->address : 'xxx');
        if ($name == 'img_url') $field = ((isset($obj->categories[0])) ? $obj->categories[0]->icon->prefix . 'bg_88' . $obj->categories[0]->icon->suffix : 'null54');
        if ($name == 'mark_url') $field = "https://ru.foursquare.com/v/".$obj->id;
        return str_replace('\'', '',$field);
    }
	
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