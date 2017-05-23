<script src="http://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/editor_polygons.js"></script>
<script type="text/javascript">
	// When the document is ready, create the map and handle clicks on it
jQuery(document).ready(function() {	
	google.maps.event.addListener(map, "click", function(evt) {
		measureAdd(evt.latLng);
	});	
	
<?php if($id != 0 && !empty($tariff_zone->points)) { ?>
		<?php $points = explode(";", $tariff_zone->points);
		unset($points[count($points) - 1]);
		foreach($points as $p) { ?>
			measureAdd(new google.maps.LatLng(<?=$p?>));
		<?php } ?>
		
<?php } ?>	
});
function get_coord(){
	$coords = '';
	for (var i = 0; i < measure.mvcPolygon.j.length; i++) {
		$coords += measure.mvcPolygon.j[i].k + ", " + measure.mvcPolygon.j[i].A + ";"
	}
	$("#TariffZones_points").val($coords);
	return true;
}

</script>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'tariff_zones-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('enctype'=>'multipart/form-data', 'onsubmit'=>'return  get_coord()'),
)); ?>


	<?php echo $form->errorSummary($tariff_zone); ?>
	<div class="row">
		<?php echo $form->labelEx($tariff_zone,'name'); ?>
		<?php echo $form->textField($tariff_zone,'name',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($tariff_zone,'name'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($tariff_zone,'is_percent'); ?>
		<?php $list_types = array('+','%');
		echo $form->dropDownList($tariff_zone, 'is_percent',$list_types);?>
		<?php echo $form->error($tariff_zone,'is_percent'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($tariff_zone,'value'); ?>
		<?php echo $form->textField($tariff_zone,'value',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($tariff_zone,'value'); ?>
	</div>
	<?php echo $form->hiddenField($tariff_zone,'points'); ?>
	<?php echo $form->error($tariff_zone,'points'); ?>
	<div id="map" class="map_canvas" style="width: 600px; height: 400px;"></div>
	<div class="row buttons">
		<?php echo CHtml::submitButton('Сохранить'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->