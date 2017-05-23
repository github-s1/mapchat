

<?php

/* @var $this PermanentUsersController */

/* @var $model PermanentUsers */

/* @var $form CActiveForm */

?>


 <h1> Cформировать уникальную скидку для клиента <?php echo $user->nickname; ?> </h1>

<div class="form">



<?php $form=$this->beginWidget('CActiveForm', array(

    'id'=>'permanent-users-permanentUsers-form',

    // Please note: When you enable ajax validation, make sure the corresponding

    // controller action is handling ajax validation correctly.

    // See class documentation of CActiveForm for details on this,

    // you need to use the performAjaxValidation()-method described there.

    'enableAjaxValidation'=>false,

)); ?>



    


    <?php echo $form->errorSummary($model); ?>

     <p> Настройки скидки: </p>
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