<?php
/**
 * Image helper functions
 *
 * @author Chris
 */
class ImageHelper {

        /**
         * Create a thumbnail of an image and returns relative path in webroot
         *
         * @param int $width
         * @param int $height
         * @param string $img
         * @param int $quality
         * @return string $path
         */
        public static function thumb($width, $height, $img, $quality = 75)
        {
                $pathinfo = pathinfo($img);
              
				$thumb_name = "thumb_".$pathinfo['filename'].'_'.$width.'_'.$height.'.'.$pathinfo['extension'];
                //$thumb_path = 'http://192.168.1.240'.$pathinfo['dirname'].'/.tmb/';
				$thumb_path = Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.'users'.DIRECTORY_SEPARATOR.'tmb'.DIRECTORY_SEPARATOR;
				if(!file_exists($thumb_path)){
                       // print_r($thumb_path); exit;
						@mkdir($thumb_path);
						//@mkdir(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name);
                }
				 
                if(!file_exists($thumb_path.$thumb_name)){
						
                        $image = Yii::app()->image->load($img);
						print_r($image); exit;
                        //$image->resize($width, $height, Image::SQUARE)->crop($width, $height)->sharpen(15)->quality($quality);
                        //$image->save($thumb_path.$thumb_name);
                }
				
                $relative_path = str_replace(YiiBase::getPathOfAlias('webroot'), '', $thumb_path.$thumb_name);
                
				return $relative_path;
        }
}
?>