<?php
//foreach ($data->fieldsKinds as $field){?>
<?php //echo $field->name; ?>
<?php //echo $field->valFieldsKinds[0]->value; ?><!--<-->
<div id="sidebar">
    <?=$this->renderPartial('/_inc/sidebarButtons')?>
    <div class="head_bar">
        <div id="sidebar_kind" class="view">
            <div class="clear border">
                <button title="Редактировать" class="edit"></button>
                <a href="#" class="back_map"><span class="back_icon"></span>На основную карту</a>
            </div>
            
            <div class="icon_info clear border">
                <div class="icon_wrap">
                    <img src="<?php echo Yii::app()->request->baseUrl; ?>/img/mark_icons/<?php echo $data->idIcon->name;?>" alt="<?php echo $data->name_ru;?>"/>
                    <span class="mini_corner"></span>
                </div>
                <p class="clear"><span class="title"><?php echo $data->name_ru;?></span><span class="cityName"><?php echo $city->name_ru;?></span></p>
                <div class="share">
                    <a href="#" class="vk"></a>
                    <a href="#" class="fb"></a>
                    <a href="#" class="hz"></a>
                </div>
            </div>
            <div class="border">
                <div class="description">
                    <p class="brief intro"><?php echo $data->description;?></p>
                    <?php if(!empty($data->description) && strlen($data->description)> 130): ?>
                    <p class="learn_more">Узнать больше >></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php //echo '<pre>'; print_r($data); echo '</pre>';die();?>
            <div class="border">
                <table class="info">
                    <tr>
                        <td>Тематика:</td>
                        <td><?php echo $theme['name'];?></td>
                    </tr>
                    <?php if (isset($data->site)):?>
                        <tr>
                            <td>Веб-сайт:</td>
                            <td><?php echo $data->site; ?></td>
                        </tr>
                    <?php endif;?>
                    <?php if (isset($data->lider)):?>
                    <tr>
                        <td>Лидер города:</td>
                        <td><?php echo $data->lider; ?></td>
                    </tr>
                    <?php endif;?>


                </table>
            </div>
            <p class="amt_icon"><?php echo $countMarks;?>
                <?php
                      $conv = new Converting;
                      $znak = $conv->pluralForm($countMarks, 'значек','значка','значков')
                ?>
                <?php echo $znak;?> вида &quot;<?php echo $data->name_ru;?>&quot; в <?php echo $city->name_ru;?>
            </p>
        </div>        
    </div>
    <?=$this->renderPartial('/_inc/idIcons')?>
</div>
<script id="appInfo">
    var appInfo = {
        kindPage: <?=$kindPage?>,
        selfUser: <?=$selfUser?>
    };
    var thirdPartyMarks = <?=ThirdPartyMark::getAllServicesMarks($city->id, true)?>;
</script>