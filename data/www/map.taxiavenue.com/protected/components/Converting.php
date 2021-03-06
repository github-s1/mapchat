<?php

class Converting {
    public static function convertModelToArray($models) {
        if (is_array($models))
            $arrayMode = TRUE;
        else {
            $models = array($models);
            $arrayMode = FALSE;
        }
        $result = array();
        foreach ($models as $model) {
            if(!$model) {
                continue;
            }
            $attributes = $model->getAttributes();
            $relations = array();
            foreach ($model->relations() as $key => $related) {
                if ($model->hasRelated($key)) {
                    $relations[$key] = convertModelToArray($model->$key);
                }
            }
            $all = array_merge($attributes, $relations);
            if ($arrayMode)
                array_push($result, $all);
            else
                $result = $all;
        }
        return $result;
    }

    public function user_array_unique ($array){
        $result = array_reduce($array, function($a, $b) {
            static $stored = array();
            $hash = md5(serialize($b));
            if (!in_array($hash, $stored)) {
                $stored[] = $hash;
                $a[] = $b;
            }
            return $a;
        }, array());
        return $result;
    }

    function pluralForm($n, $form1, $form2, $form5)
    {
        $n = abs($n) % 100;
        $n1 = $n % 10;
        if ($n > 10 && $n < 20) return $form5;
        if ($n1 > 1 && $n1 < 5) return $form2;
        if ($n1 == 1) return $form1;
        return $form5;
    }

}