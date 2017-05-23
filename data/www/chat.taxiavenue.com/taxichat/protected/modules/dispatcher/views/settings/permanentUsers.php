

<?php

/* @var $this PermanentUsersController */

/* @var $model PermanentUsers */

/* @var $form CActiveForm */

?>



<div class="form">



<?php $form=$this->beginWidget('CActiveForm', array(

    'id'=>'permanent-users-permanentUsers-form',

    // Please note: When you enable ajax validation, make sure the corresponding

    // controller action is handling ajax validation correctly.

    // See class documentation of CActiveForm for details on this,

    // you need to use the performAjaxValidation()-method described there.

    'enableAjaxValidation'=>false,

)); ?>



    <p class="note">Fields with <span class="required">*</span> are required.</p>



    <?php echo $form->errorSummary($model); ?>



    <div class="row">

        <?php echo $form->labelEx($model,'type'); ?>

        <?php echo $form->textField($model,'type'); ?>

        <?php echo $form->error($model,'type'); ?>

    </div>



    <div class="row">

        <?php echo $form->labelEx($model,'value'); ?>

        <?php echo $form->textField($model,'value'); ?>

        <?php echo $form->error($model,'value'); ?>

    </div>





    <div class="row buttons">

        <?php echo CHtml::submitButton('Submit'); ?>

    </div>



<?php $this->endWidget(); ?>



</div><!-- form -->