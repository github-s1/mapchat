<?php

class SettingsController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column1';

	/**
	 * @return array action filters
	 */
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
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('commission','SetCommission'),
				'roles'=>array('6'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionCommission()
	{
		if (empty($_POST['AgentCommission'])){
		 $id_agent = Yii::app()->user->id; 

		 $agent_commission = AgentCommission::model()->findByAttributes(array('id_agent' => $id_agent));

		   if (empty($agent_commission))
		   {
		   	$agent_commission = new AgentCommission;
            $agent_commission->commission = 20;
		   } 
		  $this->render('commission', array('model' =>  $agent_commission ));
		}else{
		   $agent_commission = AgentCommission::model()->findByAttributes(array('id_agent' => Yii::app()->user->id));
		   if (empty($agent_commission))
		   {
		   	$agent_commission = new AgentCommission;
            $agent_commission->attributes = $_POST['AgentCommission'];
            $agent_commission->id_agent = Yii::app()->user->id; 
            $agent_commission->save();
            $this->render('commission', array('model' =>  $agent_commission ));
		   }else{
		   	
		   	 $agent_commission->attributes = $_POST['AgentCommission'];
		   	 $agent_commission->save();
		   	 $this->render('commission', array('model' =>  $agent_commission ));
		   }


		}
	}
    
   public function actionSetCommission()
    {
        $id = $_POST['order_id'];
        $commission = $_POST['commission'];
        $order = Orders::model()->findByPk($id);
        if (!empty($order))
        {
        	$order->commission = $commission;
        	if($order->save())
        	{
              echo "success"; exit;
        	}else{
              echo 0; exit;
        	}
        }else{
        	echo 0; exit;
        }

    }
	


}
