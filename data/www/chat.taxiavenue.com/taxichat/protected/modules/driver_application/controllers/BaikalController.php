<?php

class BaikalController extends MobileApplicationController
{

    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            //'postOnly + delete', // we only allow deletion via POST request
        );
    }

	
     public function accessRules()
     {
        return array(
            array('allow',
                'actions'=>array('Index','Baikal', 'RepliedDrivers', 'EndBaikal','ReplyBaikal', 'CancelReplyBaikal','GetRepliedDrivers','ShowNewBaikal', 'ActualBaikals', 'AskForHelp'),
                'users'=>array('*'),
            ),
            array('deny',
                'users'=>array('*'),
            ),
        );
    }
    
	
	public function actionBaikal()
	{	
		//print_r(); exit;
		$driver_id = $this->is_authentificate();
   # $driver_id = 233; 

    
		$request = json_decode(file_get_contents('php://input'));

		$status = $request->status;
            #   $status = 0;
		$msg = ""; 
   
    $msg = $request->msg;
    if ($status == 0)
		   $msg = "Угроза жизни"; 
    
    #!empty($status) and 
		if (empty(Baikals::model()->findByAttributes(array('id_driver' => $driver_id, 'actual'=>1)))){
		$driver_status = UserStatus::model()->findByAttributes(array('id_user' => $driver_id));
		
		if($driver_status->id_status != 4) {
		  $driver_status->id_status = 4;
		  $driver_status->status_update = date('Y-m-d H:i:s', strtotime("now") + 3600);
		  $driver_status->save();
		}
		
		
               
        $Baikal = new Baikals;
        
        $Baikal->id_driver = $driver_id;
        $Baikal->status = $status;
        $Baikal->actual = 1;
        $Baikal->message =$msg;
        if ($Baikal->save()){
          $free_drivers = Dispatcher::GetNearestDrivers($driver_status->lat,$driver_status->lng,100, $driver_id);
     
          if(!empty($free_drivers)) {
              foreach($free_drivers as $driver) {        
              if ($driver->id_user != $driver_id){ 

			        	$driver->SendPush('Водителю требуется помощь.', ['push_type' => 13, 'baikal' => $Baikal->id, 'baikal_message' => $msg], false);
              }
            }
          }
          $result[] = array('result'=>'success');
        }else{
          $result[] = array('result'=>'failed');
        }
    
         $res_encode=json_encode($result);
         echo $res_encode; exit();
    //$this->render('index',array('res_encode'=>$res_encode));
       }else{
        $result[] = array('result'=>'failed');
        $res_encode=json_encode($result);
        echo $res_encode; exit();
       }
	}

	public function actionReplyBaikal($baikal=null,$type=null)
    {
     	
		$driver_id = $this->is_authentificate();
    $Baikal = Baikals::model()->findByPk($baikal);
      if (!empty($Baikal) && $type != null)
      {
        if (empty(BaikalsResponses::model()->findByAttributes(array('id_driver' => $driver_id, 'id_baikal' => $baikal)))){
        $reply = new BaikalsResponses;
        $reply->id_baikal = $baikal;
        $reply->id_driver = $driver_id;
        $reply->response = $type;
        $dangerUser = UserStatus::model()->findByAttributes(array('id_user' => $Baikal->id_driver));
        if ($reply->save())
        {
        	$result[] = array('result'=>'success', "lat" =>$dangerUser->lat, "lng" => $dangerUser->lng);
        }else{
           $result[] = array('result'=>'failed');
        }
         $res_encode=json_encode($result);
         echo $res_encode; exit();
       }else{
        $reply = BaikalsResponses::model()->findByAttributes(array('id_driver' => $driver_id, 'id_baikal' => $baikal));
        $reply->response = $type;
          if ($reply->save())
        {
         $dangerUser = UserStatus::model()->findByAttributes(array('id_user' => $Baikal->id_driver));
         $res_encode= json_encode(array('result'=>'success', "lat" =>$dangerUser->lat, "lng" => $dangerUser->lng));
        }else{
          $res_encode= json_encode(array('result'=>'failed'));
        }
        
          echo $res_encode; exit();
       }
     }else{
     	  $res_encode= json_encode(array('result'=>'failed'));
        echo $res_encode; exit();
     }

    }
    
    public function actionEndBaikal()
    {
    $driver_id = $this->is_authentificate();
    #$driver_id = 129;
    $neededBaikal = Baikals::model()->findByAttributes(array("actual"=>1, "id_driver"=>$driver_id ));
    	if (!empty($neededBaikal)){
              $neededBaikal->actual = 0;
              if ($neededBaikal->save())
              {
                  $responses = BaikalsResponses::model()->findAllByAttributes(array("id_baikal" => $neededBaikal->id));

                  if (!empty($responses)){
                  foreach ($responses as $response){
                 
                    $driver = UserStatus::model()->findByAttributes(array("id_user"=>$response->id_driver));
                    if (!empty($driver)){
					 	$driver->SendPush('Байкал завершен.', ['push_type' => 14, 'baikal' => $neededBaikal->id], false);
				    }
                  }
                  }
                 	$result[] = array('result'=>'success');
             	    $res_encode=json_encode($result);
              }else{
              	    $result[] = array('result'=>'failed1');
             	    $res_encode=json_encode($result);
              }                                      
       
          }else{
          		$result[] = array('result'=>'failed2');
             	$res_encode=json_encode($result);
                
          }
        echo $res_encode; exit();
    }
    
    public function actionActualBaikals()
    {

    	$baikals = Baikals::model()->findAllByAttributes(array('actual' => 1 ));
        
    	foreach ($baikals as $baikal) {
          $id = $baikal->id;
          $driver_id = $baikal->id_driver;
          $type = $baikal->status;

        $driver = UserStatus::model()->findByAttributes(array('id_user' => $driver_id));
    	  $res[] = array('id'=>$id, 'type' => $type,'id_driver'=> $driver_id,'driver_name' => $driver->user->name, 'lat' => $driver->lat, 'lng' => $driver->lng);
          
    	}
      if (!empty($res)){
    	  $result = array("response"=>$res);
    	  $res_encode=json_encode($result);
        echo $res_encode; exit();
       }else{
         echo 0; exit();
       }
    
    }

    public function actionCancelReplyBaikal($baikal=null)
    {
      	$driver_id = $this->is_authentificate();
     
        $baikalResponse = BaikalsResponses::model()->findByAttributes(array('id_driver' => $driver_id,'id_baikal'=>$baikal));

        if(!empty($baikalResponse))
        {
          $baikalResponse->response = 1;
          if ($baikalResponse->save()){
          	  $result[] = array('result'=>'success');
              $res_encode=json_encode($result);
          }else{
          	$result[] = array('result'=>'failed');
            $res_encode=json_encode($result);
          }
        
        }else{
        		$result[] = array('result'=>'failed');
                $res_encode=json_encode($result);
        }
         echo $res_encode; exit();
    }
    
    public function actionGetRepliedDrivers($baikal=null)
    {
      $baikal_info = Baikals::model()->findByPk($baikal);
      $driver_status = UserStatus::model()->findByAttributes(array('id_user' => $baikal_info->id_driver));
      $criteria=new CDbCriteria();
      $criteria->mergeWith(array(
        'join'=>'INNER JOIN users driver ON driver.id = t.id_user',
        'condition'=>'driver.id_type = 1',      
      ));
      $criteria->addCondition('((DEGREES(ACOS((SIN(RADIANS('.$driver_status->lat.')) * SIN(RADIANS(CAST(lat AS DECIMAL(8,2))))) + (COS(RADIANS('.$driver_status->lat.')) * COS(RADIANS(CAST(lat AS DECIMAL(8,2)))) * COS(RADIANS('.$driver_status->lng.' - CAST(lng AS DECIMAL(8,2)))))))) * 60 * 1.1515 * 1.609344) < 5');
      $criteria->addCondition("id_status = 1");
      $criteria->order = 'status_update DESC';
      $free_drivers = UserStatus::model()->findAll($criteria);
      foreach ($free_drivers as $free_driver) {
        if (empty(BaikalsResponses::model()->findByAttributes(array('id_driver'=>$free_driver->id_user))) && $free_driver->id_user != $baikal_info->id_driver)
        {
           $res[]= array('driver_id' => $free_driver->id_user, 'driver_name' => $free_driver->user->name, 'lat' => $free_driver->lat, 'lng' => $free_driver->lng, 'response' => 2, 'phone' => $free_driver->user->phone);
        }
      }
      
      $responses = BaikalsResponses::model()->findAllByAttributes(array('id_baikal'=>$baikal));
    	if (!empty($responses)){
         foreach ($responses as $response) {
       
         	  $driver=UserStatus::model()->findByAttributes(array('id_user' =>$response->id_driver));

              $res[]= array('driver_id' => $response->id_driver, 'driver_name' => $driver->user->name, 'lat' => $driver->lat, 'lng' => $driver->lng, 'response' => $response->response, 'phone' => $driver->user->phone, 'car' => $driver->user->car->marka, 'model'=>$driver->user->car->model, 'numbers' =>$driver->user->car->number);
         }
          
    	}
      if (empty($res)){
    		    $result[] = array('result'=>'failed');
            $res_encode=json_encode($result);
        }else{
          $res_encode=json_encode($res);
        }
    	
        echo $res_encode; exit();
    }

   public function actionShowNewBaikal()
   {
     $Baikal = Baikals::model()->findByAttributes(array('actual' => 1, 'dispatcher_view' => 0));
     $Baikal->dispatcher_view = 1;
     if ($Baikal->save()){
     	echo $Baikal->id;
        exit;  
     }else{
     	echo 0;
     	exit;
     }
     
   }
   public function actionAskForHelp()
   {
        if (!empty($_POST['baikal']) and !empty($_POST['driver']))
        {
          $driver = UserStatus::model()->findByAttributes(array('id_user' => $_POST['driver']));
            if (!empty($driver)){
                $driver->SendPush('Водителю требуется помощь.', ['push_type' => 3, 'driver_id' => $driver->id], false);
				echo 1; exit;

            }
            echo 0; exit;
        }
   }
  
    public function actionRepliedDrivers()
    {
      $driver_id = $this->is_authentificate();
      
      $baikal_info = Baikals::model()->findByAttributes(array('id_driver' => $driver_id, 'actual'=>1));
      if (!empty($baikal_info)){
      $driver_status = UserStatus::model()->findByAttributes(array('id_user' => $baikal_info->id_driver));
      $criteria=new CDbCriteria();
      $criteria->mergeWith(array(
        'join'=>'INNER JOIN users driver ON driver.id = t.id_user',
        'condition'=>'driver.id_type = 1',      
      ));
      $criteria->addCondition('((DEGREES(ACOS((SIN(RADIANS('.$driver_status->lat.')) * SIN(RADIANS(CAST(lat AS DECIMAL(8,2))))) + (COS(RADIANS('.$driver_status->lat.')) * COS(RADIANS(CAST(lat AS DECIMAL(8,2)))) * COS(RADIANS('.$driver_status->lng.' - CAST(lng AS DECIMAL(8,2)))))))) * 60 * 1.1515 * 1.609344) < 5');
      $criteria->addCondition("id_status = 1");
      $criteria->order = 'status_update DESC';
      $free_drivers = UserStatus::model()->findAll($criteria);
      foreach ($free_drivers as $free_driver) {
        if (empty(BaikalsResponses::model()->findByAttributes(array('id_driver'=>$free_driver->id_user))) && $free_driver->id_user != $baikal_info->id_driver)
        {
           $res[]= array('driver_id' => $free_driver->id_user, 'driver_name' => $free_driver->user->name, 'lat' => $free_driver->lat, 'lng' => $free_driver->lng, 'response' => 2, 'phone' => $free_driver->user->phone);
        }
      }
      
      $responses = BaikalsResponses::model()->findAllByAttributes(array('id_baikal'=>$baikal_info->id));
      if (!empty($responses)){
         foreach ($responses as $response) {
       
            $driver=UserStatus::model()->findByAttributes(array('id_user' =>$response->id_driver));

              $res[]= array('driver_id' => $response->id_driver, 'driver_name' => $driver->user->name, 'lat' => $driver->lat, 'lng' => $driver->lng, 'response' => $response->response, 'phone' => $driver->user->phone, 'car' => $driver->user->car->marka, 'model'=>$driver->user->car->model, 'numbers' =>$driver->user->car->number);
         }
          
      }
      if (empty($res)){
            $result[] = array('result'=>'failed');
            $res_encode=json_encode($result);
        }else{
          $res_encode=json_encode($res);
        }
      
        echo $res_encode; exit();
    }else{
         $result[] = array('result'=>'failed');
            $res_encode=json_encode($result);
    }
  }

  


}
