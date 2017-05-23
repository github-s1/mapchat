<script>
  $(document).ready(function() { 
    ClickPaginate();
  }); 
  
  function ClickPaginate() {
    $('#tabsTwo .pagination a').click(function () {
      var url = $(this).attr('href')+"/ajax/1";
      $.ajax({
        url: url,
        type: 'get',
        success: function(data){  
          $('#tabsTwo').html(data); 
          return false;
        },
        failure:function(){
        }
      });
      return false;
    });
    return false;
  } 
</script> 
  <table class="arch_orders">
  <thead>
   <tr>
    <th>Статус</th>
    <th>На когда</th>
    <th>Позывной</th>
    <th>Класс</th>
    <th>Клиент</th>
    <th>Телефон клиента</th>
    <th>Откуда</th>
    <th>Куда</th>
    <th>Цена</th>
    <th>ID</th>
    <th>Дата</th>
  </tr>
 </thead>
<?php if(!empty($orders)) { ?>
  <tbody>
  <?php foreach($orders as $i => $or){ ?>
    <?php 
      include('order_field2.php');
     ?>
  <?php } ?>
  </tbody>
</table>
<?php $this->widget('MyLinkPager', array(
        'pages' => $pages,
         )) ?>
<?php } else { ?>
   <tr>
            <td style="text-align:left;"> Нет результатов</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
        </tbody></table>
<?php } ?>