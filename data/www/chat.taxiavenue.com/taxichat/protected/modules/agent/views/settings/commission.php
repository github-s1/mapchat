<?php
/* @var $this AgentCommissionController */ 
/* @var $model AgentCommission */ 
/* @var $form CActiveForm */ 
?> 

<?php $form=$this->beginWidget('CActiveForm', array( 
    'id'=>'agent-commission-create-form', 
    // Please note: When you enable ajax validation, make sure the corresponding 
    // controller action is handling ajax validation correctly. 
    // See class documentation of CActiveForm for details on this, 
    // you need to use the performAjaxValidation()-method described there. 
    'enableAjaxValidation'=>false, 
)); ?>

  <div class="row"> 
        <?php echo $form->labelEx($model,'commission'); ?><br/>
        <?php echo $form->textField($model,'commission'); ?>
        <?php echo $form->error($model,'commission'); ?>
    </div> 
    <br/>
    <div class="row buttons"> 
        <?php echo CHtml::submitButton('Cохранить'); ?>
    </div> 

<?php $this->endWidget(); ?>

<!-- form -->