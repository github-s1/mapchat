<?php
/* @var $this IconController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Icons',
);

$this->menu=array(
	array('label'=>'Create Icon', 'url'=>array('create')),
	array('label'=>'Manage Icon', 'url'=>array('admin')),
);
?>

<h1>Icons</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
