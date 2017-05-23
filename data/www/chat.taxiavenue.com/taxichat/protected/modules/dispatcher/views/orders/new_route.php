<legend>Расчет стоимости:</legend>
<label>Цена:
	<input id="Orders_price" type="text" maxlength="8" value="<?=$price?>" readonly name="Orders[price]" style="width:90px;margin-right:100px;">
	<input id="Orders_price_distance" type="hidden" value="<?php echo($price_distance!=0?$price_distance:$price);?>" name="Orders[price_distance]">
	<input id="Orders_price_without_class" type="hidden" value="<?php echo($price_without_class!=0?$price_without_class:$price);?>" name="Orders[price_without_class]">
</label>
<label>Расстояние:
	<input id="Orders_distance" type="text" maxlength="9" value="<?=$distance?>" readonly name="Orders[distance]" style="width:90px;margin-right:100px;">
</label>