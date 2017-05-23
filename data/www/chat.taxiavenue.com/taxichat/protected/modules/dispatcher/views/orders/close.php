<form action="<?php echo Yii::app()->request->baseUrl; ?>/dispatcher/orders/FinishOrder/id/<?=$order->id?>" method="POST" >
    <div class="settings_container tarifs" id="tabs">
     <div class="clearfix ed_driver inv_tab active" id="filter">
        <div class="left_ed_driver">
          <!---  <fieldset class="ed_dr_options"> -->
                <input name="finished" type="radio"> Выполнен </input>
                <label> Время простоя
                <input name="downtime" type="text"> </input> </label>
                <input name="canceled" type="radio"> Отменён </input> 
          <!--  </fieldset> -->
        </div>
     </div>

      <div class="s_c">
        <a onclick="closePopup()" class="pop_cancel" href="javascript: void(0);">Отмена</a>
        <?php echo CHtml::submitButton("Сохранить", array('class' => 'pop_push', 'id' => 'submit_button')); ?>
      </div>
    </div>

</form>