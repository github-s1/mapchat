<?php if ($showPhotoBlock) { ?>
    <div class="photo_block clear">
        <?php
            foreach($photos as $photo){ ?>
                <a target="_blank" href="<?=Yii::app()->request->baseUrl; ?>/img/mark_photos/<?=$photo['name'];?>">
                    <img src="<?=Yii::app()->request->baseUrl; ?>/img/mark_photos/<?=$photo['name'];?>" alt="<?=$mark['description'];?>" />
                </a>
            <?php
            } ?>
    </div>
<?php } ?>