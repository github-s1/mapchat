/* 
 * Модуль для обработки пользовательских данных
 * Данные хранятся в io.socket
 */

var dbHandler = require("./dbHandler");
var _ = require('underscore');

var util = require('util');
var EventEmitter = require('events').EventEmitter;

function User() {
    EventEmitter.call(this);
    
    this.getId = function(socket) {
        if (typeof socket.dataUser.id !== 'undefined') return socket.dataUser.id;
        this.emit('disconnected', 'wrong user');
    };
    
    /**
     * Извлечь данные пользователя
     */
    this.getUserData = function(socket, fields) {
        if (typeof socket.dataUser === 'undefined') {
            this.emit('disconnected', 'wrong user');
            return;
        }
        if (typeof fields === 'undefined' || typeof fields !== 'object') return socket.dataUser;
        var data = {};
        fields.forEach(function(field){
            if (typeof socket.dataUser[field] !== 'undefined') data[field] = socket.dataUser[field];
        });
        return data;
    };
    
    /**
     * Проверка авторизации
     */
    this.checkAuthUser = function(data, callback) {

        if (!data.userId)
            return;

        var query = "SELECT u.*, a.small_photo AS small_photo, a.big_photo AS big_photo FROM `users` AS u INNER JOIN `avatar` AS a ON u.id_avatar=a.id WHERE u.id=?";
        var params = [data.userId];
        var self = this;
        // Ишем пользователя в БД
        getUserFromDb(data.userId, query, params, function(res){
            if (res === false) {
            } else {
                callback({dataUser: res,roomId: data.city.id});
            }
        });
    };
}

util.inherits(User, EventEmitter);
module.exports = User;


/*Приватные методы класса (Вынести в одтельную папку)*/

/**
 * Получить данные пользователя с БД
 */
var getUserFromDb = function(user_id, query, params, callback) {
    dbHandler.getRow(query, function(err, res){
        if(err){
            console.log(err);
            callback(false);
        }
        else if(res && _.isObject(res)){
            callback(res);
        }
        else {
            //console.log({error: "Пользователь с id " + user_id + " не найден" });
            callback(false);
        }
    }, params);
}