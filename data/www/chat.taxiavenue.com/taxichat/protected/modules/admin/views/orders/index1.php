<form action="https://www.liqpay.com/?do=clickNbuy" method="POST">
	<input type="hidden" name="operation_xml" value="<?= $xml_encoded ?>">
	<input type="hidden" name="signature" value="<?= $sign ?>">
	<input type="submit" value="Submit"/>
</form>