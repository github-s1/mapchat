<?php
$profileLink = $user['anonymous'] == "0" ? "href='#'" : "";
?>
<div class="markPage clear">
    <div class="left">
        <div class="photo_wrap">
            <?php $this->widget('application.components.widgets.MarkPhoto', array('photos' => $photos, 'mark' => $mark, 'user' => $user)); ?>
        </div>
        <?php if ($kind!='false'){?>
        <p class="kind">
            <img src="<?php echo $icon['url'];?>" alt="<?php  echo $kind['name_ru'];?>"/>
            <span><?php  echo $kind['name_ru'];?></span>
        </p>
        <?php }?>
        <p class="description"><?php echo $mark['description'];?></p>
        <?php if (isset($audio['url'])){ ?>
        <div class="audioWrap">
            <div class="audio">
                <audio preload="auto">
                    <source src="<?php echo $audio['url'];?>">
                </audio>
                <div class="player clear">
                    <span class="control left"></span>
                    <p class="duration right"><span class="progressbar"></span></p>
                </div>
            </div>
        </div>
        <?php }?>
        <div class="buttons clear">
            <a class="kind" href="<?php echo Yii::app()->request->baseUrl; ?>">
                <figure class="kind clear">
                    <img src="<?php echo $icon['url'];?>" />
                    <figcaption>
                        <span class="title"><?php  echo $kind['name_ru'];?></span>
                        <span class="total">
                            <?php $conv = new Converting; $znakKind = $conv->pluralForm(count($number_type), 'значек','значка','значков');?>
                            <!--еще <?php echo $number_type.' '.$znakKind; ?>-->
                        </span>
                    </figcaption>
                </figure>
            </a>
            <a class="profile" <?php echo $profileLink;?> >
                <figure class="user clear">
                    <?php if ($mark['anonymous'] == 'y') :?>
                        Неизвестный пользователь.
                    <?php else :?>
                        <img src="<?php if(isset($user['url_big'])) echo $user['url_big'];?>" />
                        <figcaption>
                            <?php echo $user['name'].' '.$user['family'];?>
                        </figcaption>
                    <?php endif;?>
                </figure>
            </a>
        </div>
        <div class="share clear">
            <button class="style_butt <?php if ($mark['click_spam'] > 2) echo "hide"; ?>">Убрать</button>
            <?php if($kind['id'] == -1): ?>
            <button class="style_butt green">Указать вид</button>
            <?php endif; ?>
            <a href="#" class="hz"></a>
            <a href="#" class="fb"></a>
            <a href="#" class="vk"></a>
        </div>
        <div class="comments_block">
            <h3>КОММЕНТАРИИ</h3>
            <div id="emoji" class="KEmoji_Block">
                <div class="KEmoji_Smiles_Show_Button"><div></div></div>
                <span class="placeholder">Что сказать?</span>
            </div>
            <div class="comments">
            <?php if ($comments!='false'){?>
                <?php foreach($comments as $comment){ ?>
                    <article class="clear">
                    <?php if ((isset($comment['user']['url_small']))){ ?>
                        <img class="avatar" src="<?php echo $comment['user']['url_small'];?>" title="<?php echo $comment['user']['name'].' '.$comment['user']['family']?>" />
                    <?php } ?>
                    <?php if (isset($comment['text'])){?>
                        <div class="comment"><?php echo $comment['text'];?></div>
                    <?php }?>
                    </article>
                <?php }?>
            <?php } ?>
            </div>
        </div>
    </div>
    
    <div class="right">
        <section id="markMap">
            <div></div>
            <p><?php echo $mark['address']; ?></p>
        </section>
        <div class="add_info">
            <table class="info">
                <tr>
                    <td>Добавлено:</td>
                    <td>					<?php $hours = round((time() - $mark['createDatatime'])/3600);						echo($hours);					?>ч назад					</td>
                </tr>
                <tr>
                    <td>Просмотров:</td>
                    <td><?php echo $mark['views'];?></td>
                </tr>
                <tr>
                    <td>Срок размещения:</td>
                    <td><?php echo($mark['period'] > 0?$mark['period'].' ч':'Всегда');?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript" id="appInfo">
    var appInfo = {
        markPage: <?=json_encode(array(
            'user' => $user, 'kind' => $kind, 'mark' => $mark, 'city'=>$city, 'audio' => $audio, 
            'photos' => $photos, 'points' => $points, 'icon' => $icon, 'comments' => $comments, 'type' => $type
        ))?>,
        selfUser: <?=$selfUser?>
    };
    var cityId = '<?=$city['id']?>';
    var regionId = '<?=$city['id_region']?>';
    var countryId = '<?=$city['id_country']?>';
</script>
