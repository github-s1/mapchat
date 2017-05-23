<?php

/**
 * 
 */
interface IServiceMark
{
    /**
     * Извлечь все метки из стороннего сервиса
     */
    public function fetchMarks(City $city);
    
    /**
     * Подготовить метки к отображению в формате идентичному формату нативных меток
     */
    public function getField($pos, $name);
}
