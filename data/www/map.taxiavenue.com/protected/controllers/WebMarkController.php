<?php
/**
 * Novomoskovsk
 * Synel'nykove
 * Павлоград
 */
class WebMarkController extends AjaxController
{

    /**
     * Идентификация города по данным ymap.api
     * @see baseModel.js->fetchCollectionsByCoordinates
     * @param string $country_ru
     * @param string $region_ru
     * @param string $city_ru
     * @param array $coordinates
     * @return array $cityPage
     */
    public function actionGetPageCityByCoordinates(){
        try {
            $location = $this->_getLocationByCoordinates();
            $marks = $this->_getMarksByCityId($location);
            $kinds = $this->_getKindsForMarks($marks);
            $types = Type::model()->findAll();
            
            $response = array(
                'kinds'=>$kinds,
                'types'=>$types,
                'location'=>$location->toArray(),
                'marks'=>$this->_getMarksWithRelations($marks),
                'otherMarks' => ThirdPartyMark::getAllServicesMarks($location->city->id),
                'themes'=>$this->_getThemesForKinds($kinds),
                'icons'=>$this->_getIconsForKinds($kinds),
            );
            $this->renderJSON($response);
        } 
        catch (Exception $ex) {
            $this->renderJSON(array('error'=>$ex->getMessage()));
        }
    }
    
    private function _example(){
        
//        return GeoService::Geocode("ser");
        
        
//        $ex = array();
//        $coordinates = Yii::app()->request->getPost('coordinates');
//        $lat = floatval($coordinates[0]);
//        $lng = floatval($coordinates[1]);        
//        $ex['request'] = array(
//            'coordinates'=>array($lat, $lng), 
//            'bounds'=>Yii::app()->request->getPost('bounds'),
//            'country'=>Yii::app()->request->getPost('country'), 
//            'region'=>Yii::app()->request->getPost('region'), 
//            'city'=>Yii::app()->request->getPost('city')
//        );
//        
//        $ex['geocode'] = GeoService::Geocode("Pavlohrad");
//        return $ex;
        
        
        $location = Location::setFromSearchString("ss8");
        return $location->toArray();
    }

    private function _getIconsForKinds($kinds) {
        if(empty($kinds)){
            return array();
        }
        
        $iconsIdx = array();
        foreach($kinds as $kind)
        {
            $iconsIdx[] = $kind->id_icon;
        }
        return Icon::model()->findAllByAttributes(array('id'=>$iconsIdx));
    }
    
    private function _getThemesForKinds($kinds) {
        if(empty($kinds)){
            return array();
        }
        
        $themesIdx = array();
        foreach($kinds as $kind)
        {
            $themesIdx[] = $kind->id_theme;
        }
        return Theme::model()->findAllByAttributes(array('id'=>$themesIdx));
    }
    
    private function _getKindsForMarks($marks) {
        if(empty($marks)){
            return array();
        }
        
        $kindsIdx = array();
        foreach($marks as $mark)
        {
            $kindsIdx[] = $mark->id_kind;
        }
        return Kind::model()->findAllByAttributes(array('id'=>$kindsIdx));
    }
    
    private function _getMarksByCityId($location) {
        if(empty($location->city->id)){
            return array();
        }
        
        $criteria = new CDbCriteria;
        $criteria->select='id_mark';
        $criteria->condition='id_city=:id_city';
        $criteria->params=array( ':id_city'=>$location->city->id );
        $marksCity = MarkCity::model()->findAll($criteria);
        $marksArr = array();
        foreach($marksCity as $mark)
        {
            $marksArr[] = $mark->id_mark;
        }
        return Mark::model()->findAllByAttributes(array('id'=>$marksArr, 'active'=>'Y'));
    }
    
    private function _getMarksWithRelations($marks){
        if(empty($marks)){
            return array();
        }
        $marksArr = array();
        foreach($marks as $i => $mark)
        {
            $marksArr[$i] = $mark->getAttributes();
            $marksArr[$i]['points'] = $this->_getPointsForMark($mark);
            $marksArr[$i]['photos'] = $this->_getPhotosForMark($mark);
        }
        return $marksArr;
    }
    
    private function _getPointsForMark($mark){
        $points = array();
        foreach($mark->points as $point){
            array_push($points, $point->getAttributes());
        }
        return $points;
    }
    
    private function _getPhotosForMark($mark){
        $photos = array();
        foreach($mark->photos as $photo){
            array_push($photos, $photo->name);
        }
        return $photos;
    }
    
    private function _getLocationByCoordinates() {
        $coordinates = Yii::app()->request->getPost('coordinates');
        $lat = floatval($coordinates[0]);
        $lng = floatval($coordinates[1]);

        $geoService = new GeoService();
        $location = $geoService->geocode(Yii::app()->request->getPost('city_ru'));
        
        return Location::setFromGeocoding(array(
            'coordinates'=>array($lat, $lng), 
            'bounds'=>Yii::app()->request->getPost('bounds'),
            /*'country'=>Yii::app()->request->getPost('country_ru'), 
            'region'=>Yii::app()->request->getPost('region_ru'), 
            'city'=>Yii::app()->request->getPost('city_ru')*/
            'country' => $location['country'],
            'region' => $location['region'],
            'city' => $location['city'],
        ));
    }
    
}
