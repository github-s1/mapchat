<?php
/**
 *
 */
interface IGeoService {
    
    public function translateEnRu(array $words);
    
    public function reverseGeocode($coord);
    
    public function geocode($address);
}
