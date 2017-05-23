<?php
/* @var $this MarkController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Marks',
);

$this->menu=array(
	array('label'=>'Create Mark', 'url'=>array('create')),
	array('label'=>'Manage Mark', 'url'=>array('admin')),
);
?>

<h1>Marks</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
