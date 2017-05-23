<?php

class AjaxController extends Controller
{
    /*
     * 
     */
    protected function renderJSON($data)
    {
        $this->layout=false;
        header('Content-type: application/json');
        echo CJSON::encode($data);
        Yii::app()->end();
    }
    
}
