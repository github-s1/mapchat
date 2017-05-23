<?php
class FilesOperations
{	
    public static function FilesProcessing($element = "Avatar", $index_photo = "photo")
	{
		if(!empty($_FILES)) {
			$files = $_FILES;
			$_FILES = array();
			foreach($files as $i=>$f) {
				if($i == $index_photo) {
					$_FILES[$element]['name'][$i] = $f['name'];
					$_FILES[$element]['type'][$i] = $f['type'];
					$_FILES[$element]['tmp_name'][$i] = $f['tmp_name'];
					$_FILES[$element]['error'][$i] = $f['error'];
					$_FILES[$element]['size'][$i] = $f['size'];
				}	
			}
		}
		return $_FILES; 
	}
	
}
