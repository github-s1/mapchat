
<?php
/* @var $this PermanentSettingsController */
/* @var $model PermanentSettings */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'permanent-settings-permanent-form',
    // Please note: When you enable ajax validation, make sure the corresponding
    // controller action is handling ajax validation correctly.
    // See class documentation of CActiveForm for details on this,
    // you need to use the performAjaxValidation()-method described there.
    'enableAjaxValidation'=>false,
)); ?>

  <h1> Постоянные клиенты </h1>

    <?php echo $form->errorSummary($model); ?>
    <?php if(!empty($message)): ?>
     <h2> <?php echo $message; ?> </h2><br/>
    <?php endif; ?>
    <div class="row">
        <?php echo $form->labelEx($model,'Необходимое количество заказов:'); ?><br/>
        <?php echo $form->textField($model,'orders'); ?>
        <?php echo $form->error($model,'orders'); ?>
    </div><br/>
        <p> Настройки скидок: </p>
        <br/>
        <div class="row">
        <?php echo $form->labelEx($model,'Тип скидки:'); ?><br/>
        <?php echo $form->dropDownList($model,'type',array('-','%')); ?>
        <?php echo $form->error($model,'type'); ?>
    </div>
    <br/>
    <div class="row">
        <?php echo $form->labelEx($model,'Значение скидки:'); ?><br/>
        <?php echo $form->textField($model,'value'); ?>
        <?php echo $form->error($model,'value'); ?>
    </div>




    <div class="row buttons">
        <?php echo CHtml::submitButton('Submit'); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<br/>
<div class="permanent_table"> 
    <table>
       <thead>
    <tr>
        <th>Пользователь</th>
        <th>Тип скидки</th>
        <th>Значение</th>
        <th>Удаление</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($specialSales as $key => $SS) : ?>
        <tr>
           <td> <?php echo $SS->id_customer ; ?> </td>
           <td> <?php if ($SS->type == 0){echo "-";}else{ echo "%";}  ?> </td>
           <td> <?php echo $SS->value ; ?> </td>
           <td><a href="<?php echo Yii::app()->request->baseUrl; ?>/dispatcher/customers/DeleteSale/id/<?=$SS->id?>">Удалить скидку</a> </td>
        </tr>
   <?php endforeach;  ?>
     </tbody>
    </table>

</div>