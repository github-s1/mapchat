<?php

class UserAjaxController extends AjaxController
{
    
    /*
     * 
     */
    public function actionGetActiveUser()
    {
        $result = $this->GetUserById(Yii::app()->user->id);
        $this->renderJSON(array('response' => $result));
    }

}
