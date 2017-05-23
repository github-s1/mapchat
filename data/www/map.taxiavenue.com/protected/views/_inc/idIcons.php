<div id="icons">
    <ul>
        <?php if (!empty($data['marks']) && !empty($city)): ?>
        <?php foreach ($data['marks'] as $mark): ?>
        <li class="clear">
            <figure class="left clear">
                <?php //var_dump($mark->url)?>
                <a href="<?=$city->name_en?>/<?=$mark['kind']['code'];?>"><img src="<?=Yii::app()->params['siteUrl']?>/img/mark_icons/<?php echo $mark['icon']['name'];?>" /></a>
                <figcaption>
                    <a class="clear" href="<?=$city->name_en?>/<?=$mark['kind']['code'];?>/<?=$mark['id'];?>">
                        <span class="title"><?=$mark['kind']['name_ru'];?></span>
                        <span><?php echo(time() - $mark['createDatatime'] < 86400?'('.date('H:i:s', $mark['createDatatime']).')':'');?></span>
                    </a>
                    <p class="clear"><span><?=$mark['description'];?></span><span><?=$mark['address'];?></span></p>
                </figcaption>
            </figure>
            <span class="show_icon toggle"></span>
        </li>
        <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>