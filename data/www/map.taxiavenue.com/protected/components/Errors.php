<?php
/**
 * Created by PhpStorm.
 * User: vitek25
 * Date: 21.08.14
 * Time: 10:30
 */

abstract class  Errors {
    const ERROR_AUTH = 'Ошибка авторизации';
    const ERROR_SAVE = 'Ошибка сохранения данных';
    const ERROR_USER_KIND = 'Этот вид принадлежит другому пользователю';
    const ERROR_USER_MARK = 'Эта метка принадлежит другому пользователю';
    const ERROR_KIND_EXIST = 'Такой вид уже существует';
    const ERROR_TYPE_EXIST = 'Такой тип уже существует';
    const ERROR_CODE_EXIST = 'Такой код уже существует';
    const ERROR_USER_LOGIN_EXIST = 'Пользователь с таким логином уже существует';
    const ERROR_THEME_NOT_EXIST = 'Тема не существует';
    const ERROR_KIND_NOT_EXIST = 'Вид не существует';
    const ERROR_USER_NOT_EXIST = 'Пользователь не существует';
    const ERROR_ICON_NOT_EXIST = 'Иконка не существует';
    const ERROR_TYPE_NOT_EXIST = 'Тип не существует';
    const ERROR_MARK_NOT_EXIST = 'Метка не существует';
    const ERROR_CITY_NOT_EXIST = 'Город не существует';
    const ERROR_AVATAR_NOT_EXIST = 'Аватар не существует';
    const ERROR_FILDS_EMPTY = 'Не переданы обязательные параметры';
    const ERROR_FILE = 'Файл не выбран либо большой размер файла';
    const ERROR_FILE_SAVE = 'Ошибка сохранения файла';
    const ERROR_CONFIRM_CODE = 'Время жизни кода истекло';
    const ERROR_AUTH_DATA = 'Введены не правильные данные';
    const ERROR_AUTH_TOKEN = 'Ошибка получения токена';
    const ACCOUNT_ALREADY_ACTIVE = 'Ваш аккаунт уже активирован';
    const ACCOUNT_NOT_ACTIVE = 'Вы ввели не правильный ответ. Вам необходимо заново зарегистрироваться';
    const ERROR_USER_LOGIN_INCORRECT = 'Не правильно введен логин';
    const ERROR_NOT_SAVE_IN_THIS_THEME = 'Нельзя добавить вид в эту тему';
    const ERROR_MANY_POINTS = 'В этот вид нельзя добавить больше одной точки';
    const PASSWORD_LITTLE_CHAR = 'Пароль должен содержать минимум 5 символов';
	const WRONG_ANIMAL = 'Неверное животное';
	const MISSING_DATA = 'Данные не передаются';
	const CITY_NOT_FOUND = 'Город не найден';
	const NOT_AUTHORIZED = 'Пользователь не авторизирован';
    const USER_ALREADY_ACTIVE = 'Пользователь уже активен';
	
	
	static function GetErrors($obj)
	{
		$ErrorsArray = $obj->getErrors();
		
		$errors = '';
		foreach($ErrorsArray as $arr) {
			foreach($arr as $err) {
				$errors .= $err.' ';
			}
		}
		return $errors; 
	}
}