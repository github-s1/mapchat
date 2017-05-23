<?php
/* @var $this KindController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Kinds',
);

$this->menu=array(
	array('label'=>'Create Kind', 'url'=>array('create')),
	array('label'=>'Manage Kind', 'url'=>array('admin')),
);
?>

<h1>Kinds</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
