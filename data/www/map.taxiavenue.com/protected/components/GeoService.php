<?php/** *  */class GeoService {        private $_service;        public function __construct(IGeoService $service = NULL) {        if(!empty($service)){            $this->_service = $service;        }        else {//            $this->_service = new YandexGeoService();            $this->_service = new GoogleGeoService();        }    }    public function geocode($address){        return $this->_service->geocode($address);    }    public function reverseGeocode($coord){        return $this->_service->reverseGeocode($coord);    }        public function translateEnRu(array $words){                // TODO                // Обертка (наспех)        $service = new YandexGeoService();        return $service->translateEnRu($words);    }}