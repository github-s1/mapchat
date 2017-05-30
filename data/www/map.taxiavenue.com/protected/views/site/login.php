<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle=Yii::app()->name . ' - Login';
$this->breadcrumbs=array(
	'Login',
);
?>

<h1>Login</h1>

<p>Please fill out the following form with your login credentials:</p>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'login-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<div class="row">
		<?php echo $form->labelEx($model,'login'); ?>
		<?php echo $form->textField($model,'login'); ?>
		<?php echo $form->error($model,'login'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'pass'); ?>
		<?php echo $form->passwordField($model,'pass'); ?>
		<?php echo $form->error($model,'pass'); ?>
		<p class="hint">
			Hint: You may login with <kbd>demo</kbd>/<kbd>demo</kbd> or <kbd>admin</kbd>/<kbd>admin</kbd>.
		</p>
	</div>

	<div class="row rememberMe">
		<?php echo $form->checkBox($model,'rememberMe'); ?>
		<?php echo $form->label($model,'rememberMe'); ?>
		<?php echo $form->error($model,'rememberMe'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Login'); ?>
	</div>

<?php $this->endWidget(); ?>
<h2>Do you already have an account on one of these sites? Click the logo to log in with it here:</h2>
    <button onclick="location.href='http://api.vk.com/oauth/authorize?client_id=4533710&redirect_uri=http://185.159.129.150:8085/api/socialAuth_json/vk/';">Войти ВКонтакте</button>
    <?php
    $client_id = '724330'; // ID
    $client_secret = '8536ed98701ae70454eaea10def4b2ea'; // Секретный ключ
    $redirect_uri = 'http://185.159.129.150:8085/api/socialAuth_json/mailRu/'; // Ссылка на приложение
    $params = array(
        'client_id'     => $client_id,
        'response_type' => 'code',
        'redirect_uri'  => $redirect_uri
    );
    $url='https://connect.mail.ru/oauth/authorize';
    ?>
    <?php echo $link = '<p><a href="' . $url . '?' . urldecode(http_build_query($params)) . '">Аутентификация через Mail.ru</a></p>'; ?>


    <?php

    $client_idFB = '682436305170121'; // Client ID
    $client_secretFB = '9fa9716c3077a3468737d995b10de9a3'; // Client secret
    $redirect_uriFB = 'http://185.159.129.150:8085/api/socialAuth_json/facebook/'; // Redirect URIs

    $url = 'https://www.facebook.com/dialog/oauth';

    $params = array(
        'client_id'     => $client_idFB,
        'redirect_uri'  => $redirect_uriFB,
        'response_type' => 'code',
        'scope'         => 'email,user_birthday,public_profile'
    );
    echo $link = '<p><a href="' . $url . '?' . urldecode(http_build_query($params)) . '">Аутентификация через Facebook</a></p>';


    ?>
</div><!-- form -->
