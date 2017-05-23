<?php
//запускается раз в день, снимает ежедневную комиссию со всех водителей
class Daily_commissionCommand extends CConsoleCommand {
    public function run($args) {	
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
}
