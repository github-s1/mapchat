<?php

/* 
 * Виджет для отображения фотографий на странице значка
 */

class markPhoto extends CWidget
{
    const LIMIT_IMAGES = 3;
    
    public $photos;
    public $mark;
    public $user;
    
    private $_selfUser;
    private $_defaultImg = 'placeholder.jpg';
    private $_defaultImgUrl;
    private $_showPhotoBlock = true;

    public function init() {
        $this->_selfUser = ($this->user['id'] == Yii::app()->user->id);
        
        if (!$this->_selfUser and $this->photos == 'false') $this->_showPhotoBlock = false;
        if ($this->photos == 'false') $this->photos = array();
        $this->createPhotos();
    }
    
    public function run() {
        $this->render('application.views.mark.renderPhoto', array(
            'photos' => $this->photos,
            'mark' => $this->mark,
            'showPhotoBlock' => $this->_showPhotoBlock
        ));
    }
    
    protected function createPhotos() {
        $count = count($this->photos);
        $photos = array();
        foreach ($this->photos as $value) {
            $photos[$value['position']] = $value;
        }
        for ($i = 0; $i < self::LIMIT_IMAGES; $i++) {
            if ($this->_selfUser && !isset($photos[$i])) $photos[$i] = array('name' => $this->_defaultImg); //array_push($photos, array('name' => $this->_defaultImg));
        }
        ksort($photos);
        $this->photos = $photos;
    }
}