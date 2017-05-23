<?php
/*
 * 
 */
$fullName = isset($user['name']) ? $user['name'] : "";
if (isset($user['family'])){
    $fullName .= " {$user['family']}";
}if (empty($fullName)){
    $fullName = $user['login'];
}
?>
<section id="profile" <?php echo ($isSelf) ? "class='edit'" : "" ?> >
    <section class="clear">
        <?php if ($isSelf): ?>
        <figure class="left small_photo">
            <img src="<?php echo $user['url_small']?>" alt=""/>
        </figure>
        <figure class="left big_photo">
            <img src="<?php echo $user['url_big']?>" alt=""/>
            <span class="corner"></span>
        </figure>
        <form class="right detail">
            <h1 class="clear"><?php echo $fullName?></h1>
            <input type="file" name="big_photo" />
            <input type="hidden" name="id" value="<?php echo $user['id']?>" />
            <label>
                <span>Имя:</span>
                <input disabled="disabled" type="text" name="name" value="<?php echo $user['name']?>" />
            </label>
            <label>
                <span>Фамилия:</span>
                <input disabled="disabled" type="text" name="family" value="<?php echo $user['family']?>" />
            </label>
            <label>
                <span>Логин:</span>
                <input disabled="disabled" type="text" name="login" value="<?php echo $user['login']?>" />
            </label>
            <label>
                <span>Email:</span>
                <input disabled="disabled" type="text" name="email" value="<?php echo $user['email']?>" />
            </label>
            <label>
                <span>Пол:</span>
                <input disabled="disabled" type="text" name="sex" value="<?php echo $user['sex']?>" />
            </label>
            <label>
                <span>Возраст</span>
                <input disabled="disabled" type="number" name="age" min="1" max="125" value="<?php echo $user['age']?>" />
            </label>
            <label>
                <span>Телефон:</span>
                <input disabled="disabled" type="text" name="telephone" value="<?php echo $user['telephone']?>" />
            </label>
            <label>
                <span>Город:</span>
                <input disabled="disabled" type="text" name="city" value="<?php echo $user['city']?>" />
            </label>
            <label>
                <span>Статус:</span>
                <input disabled="disabled" type="text" name="status" value="<?php echo $user['status']?>" />
            </label>
            <label>
                <span>Регистрация:</span>
                <input disabled="disabled" readonly="readonly" type="text" name="date_register" value="<?php echo $user['date_register']?>" />
            </label>
            <label class="password hidden">
                <span>Новый пароль:</span>
                <input disabled="disabled" type="password" name="pass" />
            </label>
            <label class="password_confirm hidden">
                <span>Подтвердить пароль:</span>
                <input disabled="disabled" type="password" name="pass_confirm" />
            </label>
            <label class="about">
                <span>Интересы, о себе:</span>
                <textarea disabled="disabled" name="about"><?php echo $user['about']?></textarea>
            </label>
            <div class="buttons clear">
                <button type="button" name="edit" class="style_butt right">Редактировать</button>
                <button type="button" name="cancel" class="style_butt right hidden">Отмена</button>
                <button type="button" name="save" class="style_butt right hidden">Сохранить</button>
            </div>
        </form>
        <?php else: ?>
        <figure class="left small_photo">
            <img src="<?php echo $user['url_small']?>"/>
        </figure>
        <figure class="left big_photo">
            <img src="<?php echo $user['url_big']?>"/>
        </figure>
        <div class="right detail">
            <h1 class="clear"><?php echo $fullName ?></h1>
            <p><span class="fieldName">Статус:</span>
                <span class="fieldValue"><?php echo $user['status'] ?></span>
            </p>
            <p class="social clear">
                <button type="button" name="complaint" class="style_butt">Жалоба</button>
                <a href="#" class="hz"></a>
                <a href="#" class="fb"></a>
                <a href="#" class="vk"></a>
            </p>
            <p><span class="fieldName">Пол, возраст:</span><span class="fieldValue"><?php echo "{$user['sex']}, {$user['age']} лет" ?></span></p>
            <p><span class="fieldName">Город:</span><span class="fieldValue"><?php echo $user['city']?></span></p>
            <p><span class="fieldName">Регистрация:</span><span class="fieldValue"><?php echo $user['date_register']?></span></p>
            <p><span class="fieldName">Интересы, о себе:</span><span class="fieldValue"><?php echo $user['about']?></span></p>
        </div>
        <?php endif; ?>
    </section>
</section>

<script id="appInfo">
    var appInfo = {
        selfUser: <?=$selfUser?>,
        profilePage: <?=$profilePage?>
    };
</script>