<?php
/**
 * Created by PhpStorm.
 * User: vitek25
 * Date: 13.08.14
 * Time: 16:22
 */
?>
<div class="form">
    <?php $form=$this->beginWidget('CActiveForm'); ?>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->label($model,'login'); ?>
        <?php echo $form->textField($model,'login') ?>
    </div>

    <div class="row">
        <?php echo $form->label($model,'pass'); ?>
        <?php echo $form->passwordField($model,'pass') ?>
    </div>


    <div class="row submit">
        <?php echo CHtml::submitButton('Войти'); ?>
    </div>

    <?php $this->endWidget(); ?>
</div><!-- form -->