<?php 
/*
 * 
 */
?>
<div id="sidebar" >
    <?=$this->renderPartial('/_inc/sidebarButtons')?>
    <div class="head_bar">
        <div id="sidebar_controll">
            <div class="clear">
                <button type="button" class="show_icons toggle">Скрыть всё</button>
                <button type="button" class="add_icon">Добавить</button>
            </div>
            <form class="clear">
                <select class="filter" name="where">
                    <option value="city">В городе</option>
                    <option value="map">На карте</option>
                    <option value="all">Везде</option>
                </select>
                <select class="filter" name="themes">
                    <option value="all">Все темы</option>
                </select>
                <select class="sort" name="interest">
                    <option value="new">Новые</option>
                    <option value="interest">Интересные</option>
                </select>
            </form>
        </div>
    </div>
    <div id="icons">
        <ul></ul>
    </div>
</div>
