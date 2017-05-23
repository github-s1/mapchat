<?php 
//var_dump($data)
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
                    <?php if (!empty($data['themes'])): ?>
                        <?php foreach ($data['themes'] as $theme): ?>
                        <option value="<?php echo $theme['id'];?>"><?php echo $theme['name'];?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <select class="sort" name="interest">
                    <option value="new">Новые</option>
                    <option value="interest">Интересные</option>
                </select>
            </form>
        </div>
    </div>
    <?=$this->renderPartial('/_inc/idIcons')?>
</div>
<script class="appInfo">
    var appInfo = window.appInfo || {};
    appInfo.cityPage = <?php echo $cityPage ?>;
    window.city_name_en = "<?=$city->name_en?>";
    window.mark_lists = [];
    var thirdPartyMarks = <?=ThirdPartyMark::getAllServicesMarks($city->id, true)?>;
</script>
