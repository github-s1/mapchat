<?php
/* @var $this KindController */
/* @var $model Kind */

$this->breadcrumbs=array(
	'Kinds'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Kind', 'url'=>array('index')),
	array('label'=>'Create Kind', 'url'=>array('create')),
	array('label'=>'Update Kind', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Kind', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Kind', 'url'=>array('admin')),
);
?>

<h1>View Kind #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'id_theme',
		'id_icon',
		'name_ru',
		'code',
		'description',
	),
)); ?>
