<header id="topControl" class="clear">
    <h1><a href="#" href2="<?php echo $baseURL; ?>" id="main2">Онлайн карта</a></h1>
    <form class="search clear">
        <div class="border clear">
            <input type="text" class="search_addr" placeholder="Улица или название..."/>
            <input type="submit" class="search_but" value=""/>
        </div>
        <button type="button" class="select_city">Выбор города</button>
    </form>
    <div class="right_head_bar group">
        <?php if(empty(Yii::app()->user->id)): ?>
            <p class="personal_msg logout">Вход</p>
        <?php else: ?>
            <p class="personal_msg"><?= Yii::app()->user->first_name ?></p>
        <?php endif; ?>
            <p class="chat">
                Эфир 
                +<span id="tc_countCity" title="Количество новых сообщений в городе" style="font-size: 14px">0</span>
                (+<span id="tc_countPrivate" title="Количество новых личных сообщений" style="font-size: 14px">0</span>)
            </p>
    </div>
</header>
