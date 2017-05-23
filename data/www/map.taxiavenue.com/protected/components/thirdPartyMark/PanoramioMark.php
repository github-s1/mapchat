<?php
/* 
 * Получить метки (по границам "bounds" карты) из panoramio
 * @see http://korinets.name/panoramio-api.html
 * @see http://www.panoramio.com/api/data/api.html
 */

class PanoramioMark implements IServiceMark
{
    /**
     * Url for request api
     */
    const URL = 'http://www.panoramio.com/map/get_panoramas.php';
    
    /**
     * Mode for fetch photos
     * for "set" you can use:
     *      public (popular photos)
     *      full (all photos)
     *      user ID number
     */
    const SET = 'public';
    
    /**
     * Photo size
     */
    const SIZE_PHOTO = 'medium';
    
    /**
     * Number of photos to be displayed
     */
    const FROM = 0;
    const TO = 50;
    
    public $marks = array();
    
    /**
     * Fetch marks from service. Native obj
     */
    public function fetchMarks(City $city) {
        $params = array(
            'set'   => 'public',
            'size'  => 'medium',
            'from'  => 0,
            'to'    => 50,
            'minx'  => $city->southwest_lng,
            'miny'  => $city->southwest_lat,
            'maxx'  => $city->northeast_lng,
            'maxy'  => $city->northeast_lat
        );
        $result = $this->makeRequest($params);
        if (isset($result->photos)) $this->marks = $result->photos;
        //return $this->preparedMarks($marks);
    }

    public function getField($pos, $name) {
        $obj = $this->marks[$pos];
        $field = '';
        if ($name == 'name') $field = $obj->owner_name;
        if ($name == 'lat') $field = $obj->latitude;
        if ($name == 'lng') $field = $obj->longitude;
        if ($name == 'address') $field = 'xxx';
        if ($name == 'img_url') $field = 'http://cdn.appappeal.com/pictures/15383/logo.png';//$obj->photo_file_url;
        if ($name == 'mark_url') $field = $obj->photo_url;
        return str_replace('\'', '',$field);
    }
    
    /*public function preparedMarks(array $marks) {
        $result = array();
        foreach ($marks as $item) {
            $result[] = array(
                'name' => $item->owner_name,
                'lat' => $item->latitude,
                'lng' => $item->longitude,
                'address' => 'xxx',
                'img_url' => $item->photo_file_url,
                'mark_url' => $item->owner_url
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
