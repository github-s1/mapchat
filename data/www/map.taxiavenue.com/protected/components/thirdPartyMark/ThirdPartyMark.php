<?php

/**
 * Класс сторонних меток
 * instagram, inlanger....
 */
class ThirdPartyMark
{
    protected $_service;
    protected $_city; //! Объект города
    
    static $services = array('panoramio', 'webcams', 'instagram', 'foursquare'); //! Доступные сервисы значков


    public function __construct(IServiceMark $service, $cityId) {
        $this->_service = $service;
        $this->_city = City::model()->findByPk($cityId);
    }

    /**
     * Получить метки из стороннего сервиса
     * Перегруженная функция
     * В зависимости от кол-ва передаваемых аргументов вызывает разные методы
     */
    public function getMark(){
        if (!$this->_city) return array();
        $this->_service->fetchMarks($this->_city);
        return $this->preparedMarks();
        
    }

    /**
     * Подготовка массива значков для отображения на карте
     */
    protected function preparedMarks() {
        $result = array();
        foreach ($this->_service->marks as $k => $v) {
            $result[] = array(
                'name' => $this->_service->getField($k, 'name'),
                'lat' => $this->_service->getField($k, 'lat'),
                'lng' => $this->_service->getField($k, 'lng'),
                'address' => $this->_service->getField($k, 'address'),
                'img_url' => $this->_service->getField($k, 'img_url'),
                'mark_url' => $this->_service->getField($k, 'mark_url'),
            );
        }
        return $result;
    }

        /**
     * Получить используемый сервис для отображения меток
     */
    public static function getService($name) {
        if ($name == 'instagram') return new InstagramMark();
        if ($name == 'panoramio') return new PanoramioMark();
        if ($name == 'webcams') return new WebcamsMark();
        if ($name == 'inlanger') return new InlangerMark();
	if ($name == 'foursquare') return new FoursquareMark();
        return false;
    }
    
    /**
     * Получить значки изо всех доступных сервисов
     */
    public static function getAllServicesMarks($cityId, $jsonFormat = false) {
        $allMarks = array();
        foreach (self::$services as $item) {
            $srv = self::getService($item);
            $service = new self($srv, $cityId);
            $allMarks = array_merge($allMarks, $service->getMark());
        }
        if ($jsonFormat) return json_encode ($allMarks);
        return $allMarks;
    }
}
