<?php
/* @var $this IconController */
/* @var $model Icon */

$this->breadcrumbs=array(
	'Icons'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Icon', 'url'=>array('index')),
	array('label'=>'Create Icon', 'url'=>array('create')),
	array('label'=>'View Icon', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Icon', 'url'=>array('admin')),
);
?>

<h1>Update Icon <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>