<?php
//запускается раз в неделю, снимает еженедельную комиссию со всех водителей
class Weekly_commissionCommand extends CConsoleCommand {
	public function run($args) {
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
	
}
