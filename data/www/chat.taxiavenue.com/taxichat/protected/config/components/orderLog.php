<?php 

class orderLog
{

   public static function getActualValues($log)
   {
      switch ($log->type_id) 
      {
       case 3:
      		$oldPrice = PriceClass::model()->findByPk($log->old_value);
            $old_value=$oldPrice->name;
            $newPrice = PriceClass::model()->findByPk($log->new_value);
            $new_value=$newPrice->name;
      		break;
       case 2:
            if ($log->old_value==1){
     	      $old_value = "Да";
            }else{
     	      $old_value = "Нет";
            }
            if($log->new_value==1){
           	 $new_value="Да";
            }else{
     	    $new_value="Нет";
            }
      		break;
      	case 4:
            $old_driver = Users::model()->findByPk($log->old_value);
            if (!empty($old_driver)){
            	$old_value= $old_driver->name;
            }
            $new_driver = Users::model()->findByPk($log->new_value);
            if (!empty($new_driver)){
            	$new_value= $new_driver->name;
            }
      	    break;
      	case 8:
      	    if ($log->old_value!=null){
              $old_service = Services::model()->findByPk($log->old_value);
               if (!empty($old_service)){
            	$old_value= $old_service->name;
               }
            }
             if ($log->new_value!=null){
              $new_service = Services::model()->findByPk($log->new_value);
               if (!empty($new_service)){
            	$new_value= $new_service->name;
               }
            }   
      	    break;
      	case 9:
      	    if ($log->old_value!=null){
              $old_point = Addresses::model()->findByPk($log->old_value);
               if (!empty($old_point)){
            	$old_value= $old_point->name;
               }
            }
             if ($log->new_value!=null){
              $new_point = Addresses::model()->findByPk($log->new_value);
               if (!empty($new_point)){
            	$new_value= $new_point->name;
               }
            }   
            break;
       }
     
     
        

    if (empty($old_value))
    {
     $old_value = $log->old_value;
    }
    if (empty($new_value))
    {
     $new_value = $log->new_value;
    }
    
    $res = array('old'=>$old_value, 'new'=>$new_value);
    return $res;


   }
   
   public static function ChangesLog($new, $old,$id)
    { 
    
    	$conv = new Converting;
      
    	$newOrderArray=$conv->convertModelToArray($new);
    	$oldOrder=$conv->convertModelToArray($old);
        
    	foreach ($newOrderArray as $key => $newpoint) {
    		if (ChangesTypes::model()->findByAttributes(array('cell_name' => $key)))
    		{
    			if ($newpoint != $oldOrder[$key])
    			{
    				$Changes_type=ChangesTypes::model()->findByAttributes(array('cell_name' => $key));
    				$change = new OrdersChanges;
    				$change->old_value = $oldOrder[$key];
    				$change->new_value = $newpoint;
    				$change->creator_id = Yii::app()->user->id;
    				$change->type_id = $Changes_type->id;
    				$change->order_id = $id;
    				$change->action_id = 3;
            $change->date = Date();
    				$change->save();
    		   
    			}
    		}
    	}
    }

   public static function ServiseChange($old_services,$new_services,$id)
    {

      foreach ($old_services as $service) {
         if (in_array($service, $new_services) == false)
         {
                    $change = new OrdersChanges;
    				$change->old_value = $service;
    				$change->new_value = null;
    				$change->creator_id = Yii::app()->user->id;
    				$change->type_id = 8;
    				$change->order_id = $id;
    				$change->action_id = 2;
    				$change->save();
         }        
      }
      foreach ($new_services as  $service) {
        if (in_array($service,$old_services) == false)
         {
                    $change = new OrdersChanges;
    				$change->old_value = null;
    				$change->new_value = $service;
    				$change->creator_id = Yii::app()->user->id;
    				$change->type_id = 8;
    				$change->order_id = $id;
    				$change->action_id = 1;
    				$change->save();
         }    
      }
    }
    
    public static function way_pointsChange($old,$new,$id)
    {
       foreach ($new as  $service) {
        if (in_array($service,$old) == false)
         {
                    $change = new OrdersChanges;
    				$change->old_value = null;
    				$change->new_value = $service;
    				$change->creator_id = Yii::app()->user->id;
    				$change->type_id = 9;
    				$change->order_id = $id;
    				$change->action_id = 1;
    				$change->save();
         }    
      }


    }
    public static function way_pointsDelete($id,$order)
    {
            $change = new OrdersChanges;
    				$change->old_value = $id;
    				$change->new_value = null;
    				$change->creator_id = Yii::app()->user->id;
    				$change->type_id = 9;
    				$change->order_id = $order;
    				$change->action_id = 2;
    				$change->save();
    }

    public static function getCount($id)
    {
       $criteria = new CDbCriteria();
       $criteria->addCondition("order_id = ". $id);
       return OrdersChanges::model()->count($criteria);
    }
}

?>