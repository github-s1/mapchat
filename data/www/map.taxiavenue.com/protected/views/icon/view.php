<?php
/* @var $this IconController */
/* @var $model Icon */

$this->breadcrumbs=array(
	'Icons'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List Icon', 'url'=>array('index')),
	array('label'=>'Create Icon', 'url'=>array('create')),
	array('label'=>'Update Icon', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Icon', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Icon', 'url'=>array('admin')),
);
?>

<h1>View Icon #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'width',
		'height',
	),
)); ?>
