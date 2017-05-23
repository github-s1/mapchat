<?php
/* @var $this UsersController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Водители',
);
?>

<h1>Пользователи</h1>

<?php //$this->widget('zii.widgets.CListView', array('dataProvider'=>$dataProvider, 'itemView'=>'_view')); ?>

<?php if(!empty($drivers)) { 
	foreach($drivers as $data): ?>

<div class="view">
	
	<b><?php echo CHtml::encode($data->status->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->status->name.' '.date("Y-m-d H:i:s", strtotime($data->status_update))); ?>
	<br />
	<b><?php echo CHtml::encode($data->driver->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->driver->name); ?>
	<br />

	<b><?php echo CHtml::encode($data->driver->getAttributeLabel('surname')); ?>:</b>
	<?php echo CHtml::encode($data->driver->surname); ?>
	<br />

	<b><?php echo CHtml::encode($data->driver->getAttributeLabel('patronymic')); ?>:</b>
	<?php echo CHtml::encode($data->driver->patronymic); ?>
	<br />
	<b><?php echo CHtml::encode($data->driver->getAttributeLabel('phone')); ?>:</b>
	<?php echo CHtml::encode($data->driver->phone); ?>
	<br />
	<b><?php echo CHtml::encode($data->driver->getAttributeLabel('email')); ?>:</b>
	<?php echo CHtml::encode($data->driver->email); ?>
	<br />
	<b><?php echo CHtml::encode($data->driver->getAttributeLabel('nickname')); ?>:</b>
	<?php echo CHtml::encode($data->driver->nickname); ?>
	<br />
</div>
<?php endforeach; 
} ?>
<?php $this->widget('CLinkPager', array(
    'pages' => $pages,
)) ?>
