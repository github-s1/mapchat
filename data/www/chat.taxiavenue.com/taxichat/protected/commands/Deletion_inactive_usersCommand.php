<?php
//запускается раз в день, удаляет всех неактивированых пользователей
class Deletion_inactive_usersCommand extends CConsoleCommand {
	public function run($args) {
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
