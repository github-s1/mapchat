<?php

class CronController extends Controller
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
		/*
            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions'=>array('index','editdriver'),
                'roles'=>array('1'),
            ),
		*/	
            array('allow',
                'actions'=>array('weekly_commission', 'daily_commission', 'deletion_inactive_users'),
                'users'=>array('*'),
            ),
            array('deny',
                'users'=>array('*'),
            ),
        );
    }

    public function actionWeekly_commission()
    {
        $all_drivers = Drivers::GetActiveDrivers();
		$weekly_commission = Settings::model()->findByAttributes(array('param' => 'weekly_commission'));
		if(!empty($all_drivers)) {
			foreach($all_drivers as $driver) {
			
				$comission = Drivers::GetSummRegularPayments($driver->id_user, 1);
				$comission += $weekly_commission->value;
				
				$dr = Users::model()->findByPk($driver->id_user);
				$dr->balance -= $comission;
				if($dr->save()) {
					PaymentsHistory::RemoveCommission($dr->id, $dr->balance, $dr->rating, $comission, false );
				}
			}
		}	
	}
	
	public function actionDaily_commission()
    {	
		$all_drivers = Drivers::GetActiveDrivers();
		
		if(!empty($all_drivers)) {
			foreach($all_drivers as $driver) {
				
				$comission = Drivers::GetSummRegularPayments($driver->id_user, 0);
				
				if($comission > 0) {
					$dr = Users::model()->findByPk($driver->id_user);
					$dr->balance -= $comission;
					if($dr->save()) {
						PaymentsHistory::RemoveCommission($dr->id, $dr->balance, $dr->rating, $comission, true );
					}
				}
			}	
		}
	}
	
	public function actionDeletion_inactive_users()
    {	
		$users = UserStatus::GetInactiveUsers();
		if(!empty($users)) {
			foreach($users as $user) {
				if($user->user->id_type == 1) {
					$DriverData = Drivers::GetDriverData($user->id_user);
					$driver = $DriverData['driver'];
					$car = $DriverData['car'];
					$user_status = $DriverData['user_status'];
					if($driver->delete()) {
						$user_status->delete();
						$car->delete();
						
						DriverService::model()->deleteAll('id_driver = ?' , array($user->id_user));
						DriverCommission::model()->deleteAll('id_driver = ?' , array($user->id_user));
						PaymentsHistory::model()->deleteAll('id_user = ?' , array($user->id_user));
						DriverReviews::model()->deleteAll('id_driver = ?' , array($user->id_user));
						OrderDriver::model()->deleteAll('id_driver = ?' , array($user->id_user));
					}
				} else if($user->user->id_type == 2) {
					$customer = Users::model()->findByPk($user->id_user);
					if($customer->delete()) {
						UserStatus::model()->deleteAll('id_user = ?' , array($user->id_user));
						BonusesHistory::model()->deleteAll('id_user = ?' , array($user->id_user));
						DriverReviews::model()->deleteAll('id_customer = ?' , array($user->id_user));
					} 
				}
			}
		}
	}
}