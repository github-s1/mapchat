<?php
/* @var $this UsersController */
/* @var $model Users */

$this->breadcrumbs=array(
	'Users'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List Users', 'url'=>array('index')),
	array('label'=>'Create Users', 'url'=>array('create')),
	array('label'=>'Update Users', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Users', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Users', 'url'=>array('admin')),
);
?>

<h1>View Users #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		array(       
            'label'=>'photo',
            'type'=>'raw',
            'value'=>CHtml::image(Yii::app()->params['siteUrl'].'/images/users/'.$model->id.'.'.$model->photo, $model->name,
			array(
			'width'=>'100',
			'class'=>'image',
			)),
        ),
		'user_type.name',
		'phone',
		'name',
		'surname',
		'patronymic',
		'email',
		'nickname',
		'user_status.name',
		'balance',
		array(       
            'label'=>'Car:',
            'type'=>'raw',
            'value'=>'',
        ),
		'user_car.model_car.marka_car.name',
		'user_car.model_car.name',
		'user_car.model_car.bodytype_car.name',
		'user_car.color',
	),
)); ?>
