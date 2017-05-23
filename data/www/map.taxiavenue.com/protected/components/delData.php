<?php

class delData extends Errors{

    /**удаление точки по id
     * @param $id_point
     * @param $hash
     * @return array|string
     */
    public function delPoint($id_point,$hash){
        if ($hash==Yii::app()->session->getSessionID()){
            Point::model()->deleteByPk($id_point);
            return 'success';
        }
        else
            return array('error'=>array('error_code'=>1,'error_msg'=>self::ERROR_AUTH));

    }

    public function delMark($id_user, $id_mark){
        $mark = Mark::model()->FindByPk($id_mark);
		if (!empty($mark)) {
			if($mark->id_user == $id_user){
				Audio::model()->deleteAllByAttributes(array('id_mark'=>$id_mark));
				Photo::model()->deleteAllByAttributes(array('id_mark'=>$id_mark));
				Point::model()->deleteAllByAttributes(array('id_mark'=>$id_mark));
				MarkCountry::model()->deleteAllByAttributes(array('id_mark'=>$id_mark));
				MarkRegion::model()->deleteAllByAttributes(array('id_mark'=>$id_mark));
				MarkCity::model()->deleteAllByAttributes(array('id_mark'=>$id_mark));
				Mark::model()->deleteByPk($id_mark);
				return 'success';
			} else {
				return array('error'=>array('error_code'=>2,'error_msg'=>'Вы пытаетесь удалить чужую метку.'));
			}
        }
        else {
            return array('error'=>array('error_code'=>2,'error_msg'=>'Метка с указаным id не сущетвует.'));
		}	
    }

    public function delAvatar($id_user,$hash){
        if ($hash==Yii::app()->session->getSessionID()){
            $User = Users::model()->findByPk($id_user);
            $id_avatar = $User->id_avatar;
            $User->id_avatar ='';
            $User->save();
            Avatar::model()->deleteByPk($id_avatar);
            return 'success';
        }
        else
            return array('error'=>array('error_code'=>1,'error_msg'=>self::ERROR_AUTH));
    }


}