<?php/* *  */?><section id="errorPage"><div>    <!--<h1>Ошибка, такой страницы не существует.</h1>-->    <h1><?= Yii::app()->errorHandler->error['message']?></h1>    <p>Возможно Вы ошиблись адресом или эта страница была удалена.</p>    <a href="<?= Yii::app()->request->baseUrl ?>">Перейти на главную страницу</a></div></section><div id="sidebar" >    <div class="buttons">        <button id="show_icons_panel"></button>        <button id="increase_zoom"></button>        <button id="decrease_zoom"></button>    </div>    <div class="head_bar">        <div id="sidebar_controll">            <div class="clear">                <button type="button" class="show_icons toggle">Скрыть всё</button>                <button type="button" class="add_icon">Добавить</button>            </div>            <form class="clear">                <select class="filter" name="where">                    <option value="city">В городе</option>                    <option value="map">На карте</option>                    <option value="all">Везде</option>                </select>                <select class="filter" name="themes">                    <option value="all">Все темы</option>                </select>                <select class="sort" name="interest">                    <option value="new">Новые</option>                    <option value="interest">Интересные</option>                </select>            </form>        </div>    </div></div>